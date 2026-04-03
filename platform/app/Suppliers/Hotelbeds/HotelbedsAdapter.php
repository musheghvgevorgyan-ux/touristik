<?php

namespace App\Suppliers\Hotelbeds;

use App\Suppliers\SupplierInterface;

/**
 * Hotelbeds Hotel API adapter.
 *
 * Implements the unified SupplierInterface for the Hotelbeds Booking API (v1.0)
 * and Content API (hotel-content-api/1.0). Migrated from the legacy procedural
 * functions in includes/hotelbeds.php.
 *
 * API docs: https://developer.hotelbeds.com/documentation/hotels/
 */
class HotelbedsAdapter implements SupplierInterface
{
    private array  $config;
    private string $baseUrl;
    private string $contentBaseUrl;

    /**
     * City name => [latitude, longitude]
     * Used for geolocation-based hotel search when no explicit coordinates are given.
     */
    private static array $cityCoordinates = [
        'Yerevan'         => [40.1872, 44.5152],
        'Moscow'          => [55.7558, 37.6173],
        'Sochi'           => [43.6028, 39.7342],
        'Dubai'           => [25.2048, 55.2708],
        'Istanbul'        => [41.0082, 28.9784],
        'Antalya'         => [36.8969, 30.7133],
        'Paris'           => [48.8566, 2.3522],
        'London'          => [51.5074, -0.1278],
        'Berlin'          => [52.5200, 13.4050],
        'Frankfurt'       => [50.1109, 8.6821],
        'Munich'          => [48.1351, 11.5820],
        'Rome'            => [41.9028, 12.4964],
        'Milan'           => [45.4642, 9.1900],
        'Athens'          => [37.9838, 23.7275],
        'Halkidiki'       => [40.2932, 23.4418],
        'Crete'           => [35.2401, 24.4709],
        'Tivat'           => [42.4366, 18.6964],
        'Tbilisi'         => [41.7151, 44.8271],
        'Cairo'           => [30.0444, 31.2357],
        'El Alamein'      => [30.8291, 28.9543],
        'Sharm El Sheikh' => [27.9158, 34.3300],
        'Hurghada'        => [27.2579, 33.8116],
        'Barcelona'       => [41.3874, 2.1686],
        'Madrid'          => [40.4168, -3.7038],
        'Bangkok'         => [13.7563, 100.5018],
        'Phuket'          => [7.8804, 98.3923],
        'New York'        => [40.7128, -74.0060],
        'Los Angeles'     => [34.0522, -118.2437],
        'Miami'           => [25.7617, -80.1918],
    ];

    // ───────────────────────────────────────────────────────────
    //  Construction
    // ───────────────────────────────────────────────────────────

    public function __construct()
    {
        $suppliersConfig = require BASE_PATH . '/config/suppliers.php';
        $this->config = $suppliersConfig['hotelbeds'];

        $isLive = ($this->config['environment'] ?? 'test') === 'live';

        $this->baseUrl        = $isLive
            ? 'https://api.hotelbeds.com/hotel-api/1.0'
            : 'https://api.test.hotelbeds.com/hotel-api/1.0';

        $this->contentBaseUrl = $isLive
            ? 'https://api.hotelbeds.com/hotel-content-api/1.0'
            : 'https://api.test.hotelbeds.com/hotel-content-api/1.0';
    }

    // ───────────────────────────────────────────────────────────
    //  SupplierInterface — getName
    // ───────────────────────────────────────────────────────────

    public function getName(): string
    {
        return 'hotelbeds';
    }

    // ───────────────────────────────────────────────────────────
    //  SupplierInterface — search
    // ───────────────────────────────────────────────────────────

    /**
     * Search for available hotels.
     *
     * @param array $params Keys:
     *   - checkIn      (string)  YYYY-MM-DD
     *   - checkOut     (string)  YYYY-MM-DD
     *   - latitude     (float)   optional if 'destination' is given
     *   - longitude    (float)   optional if 'destination' is given
     *   - destination  (string)  city name — used to resolve coords from static map
     *   - radius       (int)     search radius in km (default 15)
     *   - adults       (int)     default 1
     *   - children     (int)     default 0
     *   - childAges    (int[])   ages for each child
     *
     * @return array ['success' => bool, 'hotels' => array, 'error' => string]
     */
    public function search(array $params): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'hotels' => [], 'error' => 'Hotelbeds API not configured'];
        }

        $checkIn     = $params['checkIn']     ?? '';
        $checkOut    = $params['checkOut']     ?? '';
        $adults      = (int)($params['adults']    ?? 1);
        $children    = (int)($params['children']  ?? 0);
        $childAges   = $params['childAges']   ?? [];
        $radius      = (int)($params['radius']    ?? 15);
        $destination = $params['destination']  ?? '';

        // Resolve coordinates
        $latitude  = (float)($params['latitude']  ?? 0);
        $longitude = (float)($params['longitude'] ?? 0);

        if ($latitude === 0.0 && $longitude === 0.0 && $destination !== '') {
            $coords = self::$cityCoordinates[$destination] ?? null;
            if ($coords) {
                [$latitude, $longitude] = $coords;
            }
        }

        if ($latitude === 0.0 && $longitude === 0.0) {
            return ['success' => false, 'hotels' => [], 'error' => 'Unknown destination: coordinates required'];
        }

        if (!$checkIn || !$checkOut || $checkOut <= $checkIn) {
            return ['success' => false, 'hotels' => [], 'error' => 'Valid check-in and check-out dates are required'];
        }

        // ── Cache lookup ────────────────────────────────────────
        $cacheKey  = md5($destination . $latitude . $longitude . $checkIn . $checkOut . $adults . $children . implode(',', $childAges));
        $cacheFile = $this->cacheFilePath('hotels_' . $cacheKey);

        if ($cacheFile && file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                return ['success' => true, 'hotels' => $cached, 'error' => ''];
            }
        }

        // ── Build occupancy ─────────────────────────────────────
        $occupancy = [
            'rooms'    => 1,
            'adults'   => $adults,
            'children' => $children,
        ];

        if ($children > 0) {
            $paxes = [];
            for ($c = 0; $c < $children; $c++) {
                $age = isset($childAges[$c]) ? max(1, min(17, (int)$childAges[$c])) : 6;
                $paxes[] = ['type' => 'CH', 'age' => $age];
            }
            $occupancy['paxes'] = $paxes;
        }

        // ── Build request body ──────────────────────────────────
        $nights = max(1, (int)((strtotime($checkOut) - strtotime($checkIn)) / 86400));

        $requestBody = [
            'stay' => [
                'checkIn'  => $checkIn,
                'checkOut' => $checkOut,
            ],
            'occupancies'  => [$occupancy],
            'geolocation'  => [
                'latitude'  => $latitude,
                'longitude' => $longitude,
                'radius'    => $radius,
                'unit'      => 'km',
            ],
            'filter' => [
                'maxHotels'       => 50,
                'maxRatesPerRoom' => 3,
            ],
        ];

        // ── Call Availability API ───────────────────────────────
        $result = $this->makeRequest('POST', '/hotels', $requestBody);

        if ($result['code'] !== 200 || empty($result['body']['hotels']['hotels'])) {
            $errorMsg = $result['body']['error']['message'] ?? $result['error'] ?? '';
            $errorMsg = $errorMsg ?: 'Could not fetch hotel results. Please try again.';
            return ['success' => false, 'hotels' => [], 'error' => $errorMsg];
        }

        $currency   = $result['body']['hotels']['currency'] ?? 'EUR';
        $rawHotels  = $result['body']['hotels']['hotels'];
        $hotels     = [];

        // ── Parse each hotel ────────────────────────────────────
        foreach ($rawHotels as $h) {
            $minRate              = null;
            $bestBoardName        = '';
            $bestRoomName         = '';
            $bestRoomCode         = '';
            $bestRateKey          = '';
            $bestRateType         = '';
            $bestRateClass        = '';
            $bestCancellation     = [];
            $bestPromotions       = [];
            $bestRateCommentsId   = '';
            $bestSellingRate      = 0.0;
            $bestHotelMandatory   = false;
            $allRooms             = [];

            if (!empty($h['rooms'])) {
                foreach ($h['rooms'] as $room) {
                    if (empty($room['rates'])) {
                        continue;
                    }
                    foreach ($room['rates'] as $rate) {
                        // Skip packaging/opaque rates — they must be combined with
                        // other products and cannot be booked standalone.
                        if (!empty($rate['packaging'])) {
                            continue;
                        }

                        $net = (float)($rate['net'] ?? 0);

                        // Collect every room option for the detail view
                        $allRooms[] = [
                            'room_name'             => $room['name'] ?? '',
                            'room_code'             => $room['code'] ?? '',
                            'board'                 => $rate['boardName'] ?? '',
                            'board_code'            => $rate['boardCode'] ?? '',
                            'price'                 => $net,
                            'rate_key'              => $rate['rateKey'] ?? '',
                            'rate_type'             => $rate['rateType'] ?? 'BOOKABLE',
                            'cancellation_policies' => $rate['cancellationPolicies'] ?? [],
                            'promotions'            => $rate['promotions'] ?? [],
                            'rate_comments_id'      => $rate['rateCommentsId'] ?? '',
                            'selling_rate'          => (float)($rate['sellingRate'] ?? $net),
                            'hotel_mandatory'       => $rate['hotelMandatory'] ?? false,
                        ];

                        // Track cheapest rate
                        if ($minRate === null || $net < $minRate) {
                            $minRate              = $net;
                            $bestBoardName        = $rate['boardName'] ?? '';
                            $bestRoomName         = $room['name'] ?? '';
                            $bestRoomCode         = $room['code'] ?? '';
                            $bestRateKey          = $rate['rateKey'] ?? '';
                            $bestRateType         = $rate['rateType'] ?? 'BOOKABLE';
                            $bestRateClass        = $rate['rateClass'] ?? '';
                            $bestCancellation     = $rate['cancellationPolicies'] ?? [];
                            $bestPromotions       = $rate['promotions'] ?? [];
                            $bestRateCommentsId   = $rate['rateCommentsId'] ?? '';
                            $bestSellingRate      = (float)($rate['sellingRate'] ?? $net);
                            $bestHotelMandatory   = $rate['hotelMandatory'] ?? false;
                        }
                    }
                }
            }

            if ($minRate === null) {
                continue; // no bookable rates found
            }

            // Use the first image from the availability response as a fallback;
            // Content API images will overwrite this below.
            $fallbackImage = '';
            if (!empty($h['images'][0]['path'])) {
                $fallbackImage = 'https://photos.hotelbeds.com/giata/bigger/' . $h['images'][0]['path'];
            }

            $hotels[] = [
                'name'                  => $h['name'] ?? 'Hotel',
                'code'                  => $h['code'] ?? '',
                'stars'                 => $h['categoryName'] ?? '',
                'stars_num'             => (int)($h['categoryCode'] ?? 0),
                'price'                 => $minRate,
                'selling_rate'          => $bestSellingRate,
                'price_per_night'       => round($minRate / max($nights, 1), 2),
                'nights'                => $nights,
                'board'                 => $bestBoardName,
                'room'                  => $bestRoomName,
                'room_code'             => $bestRoomCode,
                'currency'              => $currency,
                'image'                 => $fallbackImage,
                'rate_key'              => $bestRateKey,
                'rate_type'             => $bestRateType,
                'rate_class'            => $bestRateClass,
                'cancellation_policies' => $bestCancellation,
                'promotions'            => $bestPromotions,
                'rate_comments_id'      => $bestRateCommentsId,
                'hotel_mandatory'       => $bestHotelMandatory,
                'all_rooms'             => $allRooms,
                'check_in'             => $checkIn,
                'check_out'            => $checkOut,
            ];
        }

        // ── Fetch higher-quality images from Content API ────────
        $hotels = $this->enrichWithContentImages($hotels);

        // ── Sort by price ascending ─────────────────────────────
        usort($hotels, fn(array $a, array $b) => $a['price'] <=> $b['price']);

        // ── Write cache ─────────────────────────────────────────
        if (!empty($hotels) && $cacheFile) {
            file_put_contents($cacheFile, json_encode($hotels));
        }

        return ['success' => true, 'hotels' => $hotels, 'error' => ''];
    }

    // ───────────────────────────────────────────────────────────
    //  SupplierInterface — checkRate
    // ───────────────────────────────────────────────────────────

    /**
     * Verify that a rate is still available and get the final price.
     *
     * @param string $rateKey The Hotelbeds rate key
     * @return array Unified result with hotel, room, rate info or error
     */
    public function checkRate(string $rateKey): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Hotelbeds API not configured'];
        }

        $result = $this->makeRequest('POST', '/checkrates', [
            'rooms' => [
                ['rateKey' => $rateKey],
            ],
        ]);

        if ($result['code'] === 200 && !empty($result['body']['hotel'])) {
            $hotel = $result['body']['hotel'];
            $room  = $hotel['rooms'][0]  ?? [];
            $rate  = $room['rates'][0]   ?? [];

            return [
                'success' => true,
                'hotel' => [
                    'name'        => $hotel['name'] ?? '',
                    'code'        => $hotel['code'] ?? '',
                    'category'    => $hotel['categoryName'] ?? '',
                    'destination' => $hotel['destinationName'] ?? '',
                    'image'       => !empty($hotel['image'])
                        ? 'https://photos.hotelbeds.com/giata/bigger/' . $hotel['image']
                        : '',
                ],
                'room' => [
                    'name' => $room['name'] ?? '',
                    'code' => $room['code'] ?? '',
                ],
                'rate' => [
                    'key'                   => $rate['rateKey'] ?? $rateKey,
                    'net'                   => (float)($rate['net'] ?? 0),
                    'selling_rate'          => (float)($rate['sellingRate'] ?? $rate['net'] ?? 0),
                    'currency'              => $hotel['currency'] ?? 'EUR',
                    'board'                 => $rate['boardName'] ?? '',
                    'board_code'            => $rate['boardCode'] ?? '',
                    'cancellation_policies' => $rate['cancellationPolicies'] ?? [],
                    'check_in'             => $hotel['checkIn'] ?? '',
                    'check_out'            => $hotel['checkOut'] ?? '',
                    'rooms'                 => $rate['rooms'] ?? 1,
                    'adults'                => $rate['adults'] ?? 1,
                    'children'              => $rate['children'] ?? 0,
                    'rateComments'          => $rate['rateComments'] ?? '',
                ],
            ];
        }

        // Error handling: 400 = bad request, 500+ = product unavailable (restart search)
        $errorMsg = $result['body']['error']['message'] ?? '';

        if ($result['code'] >= 500) {
            return [
                'success' => false,
                'error'   => $errorMsg ?: 'Rate is no longer available. Please search again.',
                'restart' => true,
            ];
        }

        return [
            'success' => false,
            'error'   => $errorMsg ?: 'CheckRate failed. Please try again.',
            'restart' => false,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  SupplierInterface — book
    // ───────────────────────────────────────────────────────────

    /**
     * Create a booking (confirm the reservation).
     *
     * @param array $details Keys:
     *   - rateKey  (string)
     *   - holder   (array)  ['name' => string, 'surname' => string]
     *   - rooms    (array)  each with 'paxes' => [['roomId','type','name','surname'], ...]
     *   - email    (string) guest email
     *   - phone    (string) guest phone
     *   - remark   (string) special requests
     *   - clientReference (string) optional — auto-generated if empty
     *
     * @return array Unified booking result or error
     */
    public function book(array $details): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Hotelbeds API not configured'];
        }

        $rateKey  = $details['rateKey']  ?? '';
        $holder   = $details['holder']   ?? [];
        $rooms    = $details['rooms']    ?? [];
        $remark   = $details['remark']   ?? '';
        $clientRef = $details['clientReference'] ?? $this->generateClientReference();

        // Build the API request body
        $body = [
            'holder' => [
                'name'    => $holder['name']    ?? '',
                'surname' => $holder['surname'] ?? '',
            ],
            'rooms'           => [],
            'clientReference' => $clientRef,
            'remark'          => $remark,
        ];

        foreach ($rooms as $room) {
            $roomData = [
                'rateKey' => $rateKey,
                'paxes'   => [],
            ];
            foreach ($room['paxes'] ?? [] as $pax) {
                $paxEntry = [
                    'roomId'  => $pax['roomId']  ?? 1,
                    'type'    => $pax['type']     ?? 'AD',
                    'name'    => $pax['name']     ?? '',
                    'surname' => $pax['surname']  ?? '',
                ];
                // Include age for children
                if (($pax['type'] ?? '') === 'CH' && isset($pax['age'])) {
                    $paxEntry['age'] = (int)$pax['age'];
                }
                $roomData['paxes'][] = $paxEntry;
            }
            $body['rooms'][] = $roomData;
        }

        // If no rooms were explicitly provided, create a single room with the holder as the pax
        if (empty($body['rooms'])) {
            $body['rooms'][] = [
                'rateKey' => $rateKey,
                'paxes'   => [[
                    'roomId'  => 1,
                    'type'    => 'AD',
                    'name'    => $holder['name']    ?? '',
                    'surname' => $holder['surname'] ?? '',
                ]],
            ];
        }

        $result = $this->makeRequest('POST', '/bookings', $body);

        if ($result['code'] === 200 && !empty($result['body']['booking'])) {
            $booking  = $result['body']['booking'];
            $hotel    = $booking['hotel']    ?? [];
            $supplier = $hotel['supplier']   ?? [];

            return [
                'success' => true,
                'booking' => [
                    'reference'        => $booking['reference'] ?? '',
                    'client_reference' => $booking['clientReference'] ?? $clientRef,
                    'status'           => $booking['status'] ?? '',
                    'hotel'            => $hotel['name'] ?? '',
                    'hotel_code'       => $hotel['code'] ?? '',
                    'hotel_category'   => $hotel['categoryName'] ?? '',
                    'hotel_address'    => ($hotel['address'] ?? '') ?: ($hotel['destinationName'] ?? ''),
                    'hotel_destination' => $hotel['destinationName'] ?? '',
                    'hotel_phone'      => $hotel['phoneNumber'] ?? '',
                    'check_in'         => $hotel['checkIn'] ?? '',
                    'check_out'        => $hotel['checkOut'] ?? '',
                    'total'            => (float)($booking['totalNet'] ?? 0),
                    'currency'         => $booking['currency'] ?? 'EUR',
                    'holder'           => trim(($booking['holder']['name'] ?? '') . ' ' . ($booking['holder']['surname'] ?? '')),
                    'rooms'            => $hotel['rooms'] ?? [],
                    'created_at'       => $booking['creationDate'] ?? date('Y-m-d'),
                    'remark'           => $booking['remark'] ?? '',
                    'supplier_name'    => $supplier['name'] ?? 'Hotelbeds',
                    'supplier_vat'     => $supplier['vatNumber'] ?? '',
                ],
            ];
        }

        // Error handling: 400 = don't retry same request, 500+ = unavailable (restart from search)
        $errorMsg = $result['body']['error']['message'] ?? '';

        if ($result['code'] >= 500) {
            return [
                'success' => false,
                'error'   => $errorMsg ?: 'This hotel is no longer available. Please search again.',
                'restart' => true,
            ];
        }

        return [
            'success' => false,
            'error'   => $errorMsg ?: 'Booking failed. Please check your details and try again.',
            'restart' => false,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  SupplierInterface — cancel
    // ───────────────────────────────────────────────────────────

    /**
     * Cancel a booking by supplier reference.
     *
     * @param string $reference Hotelbeds booking reference
     * @return array ['success' => bool, 'cancellation_ref' => string, ...]
     */
    public function cancel(string $reference): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Hotelbeds API not configured'];
        }

        $endpoint = '/bookings/' . urlencode($reference) . '?cancellationFlag=CANCELLATION';
        $result   = $this->makeRequest('DELETE', $endpoint);

        if ($result['code'] === 200 && !empty($result['body']['booking'])) {
            $booking = $result['body']['booking'];
            return [
                'success'          => true,
                'cancellation_ref' => $booking['reference'] ?? $reference,
                'status'           => $booking['status'] ?? 'CANCELLED',
                'booking'          => $booking,
            ];
        }

        $errorMsg = $result['body']['error']['message'] ?? 'Cancellation failed.';
        return ['success' => false, 'error' => $errorMsg];
    }

    // ───────────────────────────────────────────────────────────
    //  SupplierInterface — getBooking
    // ───────────────────────────────────────────────────────────

    /**
     * Retrieve booking details by supplier reference.
     *
     * @param string $reference Hotelbeds booking reference
     * @return array Booking details or error
     */
    public function getBooking(string $reference): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Hotelbeds API not configured'];
        }

        $result = $this->makeRequest('GET', '/bookings/' . urlencode($reference));

        if ($result['code'] === 200 && !empty($result['body']['booking'])) {
            return ['success' => true, 'booking' => $result['body']['booking']];
        }

        return ['success' => false, 'error' => 'Booking not found.'];
    }

    // ───────────────────────────────────────────────────────────
    //  Public helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Resolve city coordinates from the static map.
     * Useful when the caller wants to check if a destination is supported.
     */
    public static function resolveCoordinates(string $city): ?array
    {
        return self::$cityCoordinates[$city] ?? null;
    }

    /**
     * Get all supported destination city names.
     */
    public static function supportedDestinations(): array
    {
        return array_keys(self::$cityCoordinates);
    }

    // ───────────────────────────────────────────────────────────
    //  Private — HTTP transport
    // ───────────────────────────────────────────────────────────

    /**
     * Make an authenticated request to the Hotelbeds API.
     *
     * @param string     $method   HTTP method: GET, POST, DELETE
     * @param string     $endpoint Path relative to baseUrl (e.g. '/hotels')
     * @param array|null $body     Request body (JSON-encoded for POST)
     *
     * @return array ['code' => int, 'body' => array|null, 'error' => string]
     */
    private function makeRequest(string $method, string $endpoint, ?array $body = null): array
    {
        $apiKey    = $this->config['api_key']    ?? '';
        $apiSecret = $this->config['api_secret'] ?? '';

        // HMAC signature: SHA-256 of apiKey + apiSecret + Unix timestamp (seconds)
        $signature = hash('sha256', $apiKey . $apiSecret . time());

        $url     = $this->baseUrl . $endpoint;
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
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_ENCODING       => 'gzip',
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        if ($method === 'POST' && $body !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        return [
            'code'  => $httpCode,
            'body'  => $response ? json_decode($response, true) : null,
            'error' => $curlError,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  Private — Content API image enrichment
    // ───────────────────────────────────────────────────────────

    /**
     * Fetch higher-quality images from the Hotelbeds Content API and
     * merge them into the hotel results array.
     *
     * Prefers GEN (general) or EXT (exterior) image types; falls back
     * to the first available image.
     */
    private function enrichWithContentImages(array $hotels): array
    {
        $hotelCodes = array_column($hotels, 'code');

        if (empty($hotelCodes)) {
            return $hotels;
        }

        $apiKey    = $this->config['api_key']    ?? '';
        $apiSecret = $this->config['api_secret'] ?? '';
        $signature = hash('sha256', $apiKey . $apiSecret . time());

        $codesParam = implode(',', $hotelCodes);
        $contentUrl = $this->contentBaseUrl
            . '/hotels?codes=' . $codesParam
            . '&fields=images&language=ENG';

        $ch = curl_init($contentUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Accept-Encoding: gzip',
                'Api-key: ' . $apiKey,
                'X-Signature: ' . $signature,
            ],
            CURLOPT_ENCODING       => 'gzip',
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return $hotels; // graceful fallback — keep the availability-response images
        }

        $data = json_decode($response, true);
        if (empty($data['hotels'])) {
            return $hotels;
        }

        // Build a code => best-image-URL map
        $imageMap = [];
        foreach ($data['hotels'] as $imgHotel) {
            $code = $imgHotel['code'] ?? '';
            if (!$code || empty($imgHotel['images'])) {
                continue;
            }

            // Prefer GEN (general) or EXT (exterior) images
            $bestImg = '';
            foreach ($imgHotel['images'] as $img) {
                $type = $img['type']['code'] ?? '';
                if ($type === 'GEN' || $type === 'EXT') {
                    $bestImg = $img['path'];
                    break;
                }
            }
            if (!$bestImg) {
                $bestImg = $imgHotel['images'][0]['path'] ?? '';
            }
            if ($bestImg) {
                $imageMap[$code] = 'https://photos.hotelbeds.com/giata/bigger/' . $bestImg;
            }
        }

        // Merge
        foreach ($hotels as &$hotel) {
            if (isset($imageMap[$hotel['code']])) {
                $hotel['image'] = $imageMap[$hotel['code']];
            }
        }
        unset($hotel);

        return $hotels;
    }

    // ───────────────────────────────────────────────────────────
    //  Private — Utilities
    // ───────────────────────────────────────────────────────────

    /**
     * Check whether the API credentials are configured.
     */
    private function isConfigured(): bool
    {
        return !empty($this->config['api_key']) && !empty($this->config['api_secret']);
    }

    /**
     * Generate a unique client reference for a booking (TK-XXXXXXXX).
     */
    private function generateClientReference(): string
    {
        return 'TK-' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Get the full path for a cache file, creating the cache directory if needed.
     *
     * @return string|null File path, or null if cache dir is not writable
     */
    private function cacheFilePath(string $filename): ?string
    {
        $cacheDir = BASE_PATH . '/cache';

        if (!is_dir($cacheDir)) {
            if (!@mkdir($cacheDir, 0755, true)) {
                return null;
            }
        }

        return $cacheDir . '/' . $filename . '.json';
    }
}
