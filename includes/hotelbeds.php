<?php

function hbConfig() {
    $configFile = __DIR__ . '/../config/hotelbeds.json';
    if (!file_exists($configFile)) return null;
    $config = json_decode(file_get_contents($configFile), true);
    if (empty($config['api_key']) || empty($config['api_secret'])) return null;
    return $config;
}

function hbBaseUrl() {
    $config = hbConfig();
    $env = $config['environment'] ?? 'test';
    return $env === 'live'
        ? 'https://api.hotelbeds.com/hotel-api/1.0'
        : 'https://api.test.hotelbeds.com/hotel-api/1.0';
}

function hbSignature($apiKey, $apiSecret) {
    return hash('sha256', $apiKey . $apiSecret . time());
}

function hbRequest($method, $url, $apiKey, $apiSecret, $body = null) {
    $signature = hbSignature($apiKey, $apiSecret);
    $headers = [
        'Accept: application/json',
        'Accept-Encoding: gzip',
        'Content-Type: application/json',
        'Api-key: ' . $apiKey,
        'X-Signature: ' . $signature,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_ENCODING => 'gzip',
        CURLOPT_TIMEOUT => 60,
    ]);

    if ($method === 'POST' && $body !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'body' => $response ? json_decode($response, true) : null,
        'error' => $error,
    ];
}

/**
 * CheckRate — verify rate is still valid and get final price before booking
 */
function hbCheckRate($rateKey) {
    $config = hbConfig();
    if (!$config) return ['success' => false, 'error' => 'API not configured'];

    $body = [
        'rooms' => [
            ['rateKey' => $rateKey]
        ]
    ];

    $result = hbRequest('POST', hbBaseUrl() . '/checkrates', $config['api_key'], $config['api_secret'], $body);

    if ($result['code'] === 200 && !empty($result['body']['hotel'])) {
        $hotel = $result['body']['hotel'];
        $room = $hotel['rooms'][0] ?? null;
        $rate = $room['rates'][0] ?? null;

        return [
            'success' => true,
            'hotel' => [
                'name' => $hotel['name'] ?? '',
                'code' => $hotel['code'] ?? '',
                'category' => $hotel['categoryName'] ?? '',
                'destination' => $hotel['destinationName'] ?? '',
                'image' => !empty($hotel['image']) ? 'https://photos.hotelbeds.com/giata/bigger/' . $hotel['image'] : '',
            ],
            'room' => [
                'name' => $room['name'] ?? '',
                'code' => $room['code'] ?? '',
            ],
            'rate' => [
                'key' => $rate['rateKey'] ?? $rateKey,
                'net' => (float)($rate['net'] ?? 0),
                'selling_rate' => (float)($rate['sellingRate'] ?? $rate['net'] ?? 0),
                'currency' => $hotel['currency'] ?? 'EUR',
                'board' => $rate['boardName'] ?? '',
                'board_code' => $rate['boardCode'] ?? '',
                'cancellation_policies' => $rate['cancellationPolicies'] ?? [],
                'check_in' => $hotel['checkIn'] ?? '',
                'check_out' => $hotel['checkOut'] ?? '',
                'rooms' => $rate['rooms'] ?? 1,
                'adults' => $rate['adults'] ?? 1,
                'children' => $rate['children'] ?? 0,
                'rateComments' => $rate['rateComments'] ?? '',
            ],
        ];
    }

    $errorMsg = $result['body']['error']['message'] ?? 'Rate is no longer available. Please search again.';
    return ['success' => false, 'error' => $errorMsg];
}

/**
 * Booking — confirm the reservation
 */
function hbBook($rateKey, $holder, $rooms) {
    $config = hbConfig();
    if (!$config) return ['success' => false, 'error' => 'API not configured'];

    $body = [
        'holder' => [
            'name' => $holder['name'],
            'surname' => $holder['surname'],
        ],
        'rooms' => [],
        'clientReference' => 'TOUR-' . strtoupper(bin2hex(random_bytes(4))),
        'remark' => $holder['remark'] ?? '',
    ];

    foreach ($rooms as $room) {
        $roomData = [
            'rateKey' => $rateKey,
            'paxes' => [],
        ];
        foreach ($room['paxes'] as $pax) {
            $roomData['paxes'][] = [
                'roomId' => $pax['roomId'] ?? 1,
                'type' => $pax['type'], // AD or CH
                'name' => $pax['name'],
                'surname' => $pax['surname'],
            ];
        }
        $body['rooms'][] = $roomData;
    }

    $result = hbRequest('POST', hbBaseUrl() . '/bookings', $config['api_key'], $config['api_secret'], $body);

    if ($result['code'] === 200 && !empty($result['body']['booking'])) {
        $booking = $result['body']['booking'];
        $hotel = $booking['hotel'] ?? [];
        $supplier = $hotel['supplier'] ?? [];

        return [
            'success' => true,
            'booking' => [
                'reference' => $booking['reference'] ?? '',
                'client_reference' => $booking['clientReference'] ?? '',
                'status' => $booking['status'] ?? '',
                'hotel' => $hotel['name'] ?? '',
                'hotel_code' => $hotel['code'] ?? '',
                'hotel_category' => $hotel['categoryName'] ?? '',
                'hotel_address' => ($hotel['address'] ?? '') ?: ($hotel['destinationName'] ?? ''),
                'hotel_destination' => $hotel['destinationName'] ?? '',
                'hotel_phone' => $hotel['phoneNumber'] ?? '',
                'check_in' => $hotel['checkIn'] ?? '',
                'check_out' => $hotel['checkOut'] ?? '',
                'total' => (float)($booking['totalNet'] ?? 0),
                'currency' => $booking['currency'] ?? 'EUR',
                'holder' => ($booking['holder']['name'] ?? '') . ' ' . ($booking['holder']['surname'] ?? ''),
                'rooms' => $hotel['rooms'] ?? [],
                'created_at' => $booking['creationDate'] ?? date('Y-m-d'),
                'remark' => $booking['remark'] ?? '',
                'supplier_name' => $supplier['name'] ?? 'Hotelbeds',
                'supplier_vat' => $supplier['vatNumber'] ?? '',
            ],
        ];
    }

    $errorMsg = $result['body']['error']['message'] ?? 'Booking failed. Please try again.';
    return ['success' => false, 'error' => $errorMsg];
}

/**
 * Get booking details
 */
function hbGetBooking($reference) {
    $config = hbConfig();
    if (!$config) return ['success' => false, 'error' => 'API not configured'];

    $result = hbRequest('GET', hbBaseUrl() . '/bookings/' . urlencode($reference), $config['api_key'], $config['api_secret']);

    if ($result['code'] === 200 && !empty($result['body']['booking'])) {
        return ['success' => true, 'booking' => $result['body']['booking']];
    }

    return ['success' => false, 'error' => 'Booking not found.'];
}

/**
 * Cancel booking
 */
function hbCancelBooking($reference) {
    $config = hbConfig();
    if (!$config) return ['success' => false, 'error' => 'API not configured'];

    $result = hbRequest('DELETE', hbBaseUrl() . '/bookings/' . urlencode($reference) . '?cancellationFlag=CANCELLATION', $config['api_key'], $config['api_secret']);

    if ($result['code'] === 200 && !empty($result['body']['booking'])) {
        return ['success' => true, 'booking' => $result['body']['booking']];
    }

    $errorMsg = $result['body']['error']['message'] ?? 'Cancellation failed.';
    return ['success' => false, 'error' => $errorMsg];
}
