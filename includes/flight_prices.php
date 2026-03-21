<?php

function getDestinationFlightPrice($destinationName) {
    // Map destination names to city names for IATA lookup
    $cityMap = [
        'Paris, France' => 'Paris',
        'Tokyo, Japan' => null, // No IATA in our system
        'Bali, Indonesia' => null,
        'Rome, Italy' => 'Rome',
        'New York, USA' => 'New York',
        'Maldives' => null,
        'Dubai, UAE' => 'Dubai',
        'Istanbul, Turkey' => 'Istanbul',
        'Antalya, Turkey' => 'Antalya',
        'London, UK' => 'London',
        'Barcelona, Spain' => 'Barcelona',
        'Madrid, Spain' => 'Madrid',
        'Berlin, Germany' => 'Berlin',
        'Frankfurt, Germany' => 'Frankfurt',
        'Munich, Germany' => 'Munich',
        'Athens, Greece' => 'Athens',
        'Halkidiki, Greece' => 'Halkidiki',
        'Crete, Greece' => 'Crete',
        'Milan, Italy' => 'Milan',
        'Tivat, Montenegro' => 'Tivat',
        'Moscow, Russia' => 'Moscow',
        'Sochi, Russia' => 'Sochi',
        'Tbilisi, Georgia' => 'Tbilisi',
        'Cairo, Egypt' => 'Cairo',
        'El Alamein, Egypt' => 'El Alamein',
        'Sharm El Sheikh, Egypt' => 'Sharm El Sheikh',
        'Hurghada, Egypt' => 'Hurghada',
        'Bangkok, Thailand' => 'Bangkok',
        'Phuket, Thailand' => 'Phuket',
        'Los Angeles, USA' => 'Los Angeles',
        'Miami, USA' => 'Miami',
    ];

    $iataCodes = [
        'Yerevan' => 'EVN', 'Moscow' => 'SVO', 'Sochi' => 'AER', 'Dubai' => 'DXB',
        'Istanbul' => 'IST', 'Antalya' => 'AYT', 'Paris' => 'CDG', 'London' => 'LHR',
        'Berlin' => 'BER', 'Frankfurt' => 'FRA', 'Munich' => 'MUC', 'Rome' => 'FCO',
        'Milan' => 'MXP', 'Athens' => 'ATH', 'Halkidiki' => 'SKG', 'Crete' => 'HER',
        'Tivat' => 'TIV', 'Tbilisi' => 'TBS', 'Cairo' => 'CAI', 'El Alamein' => 'DBB',
        'Sharm El Sheikh' => 'SSH', 'Hurghada' => 'HRG', 'Barcelona' => 'BCN',
        'Madrid' => 'MAD', 'Bangkok' => 'BKK', 'Phuket' => 'HKT', 'New York' => 'JFK',
        'Los Angeles' => 'LAX', 'Miami' => 'MIA'
    ];

    // Try exact match first, then partial match
    $city = $cityMap[$destinationName] ?? null;
    if (!$city) {
        // Try extracting city from "City, Country" format
        $parts = explode(',', $destinationName);
        $city = trim($parts[0]);
    }

    if (!$city || !isset($iataCodes[$city])) {
        return null;
    }

    $toCode = $iataCodes[$city];
    $fromCode = 'EVN'; // Yerevan as origin

    // Cache per destination (24 hours)
    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);
    $cacheFile = $cacheDir . '/dest_price_' . $toCode . '.json';

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached && isset($cached['price'])) {
            return $cached['price'];
        }
    }

    // Fetch from Travelpayouts
    $configFile = __DIR__ . '/../config/travelpayouts.json';
    if (!file_exists($configFile)) return null;
    $config = json_decode(file_get_contents($configFile), true);
    $token = $config['api_token'] ?? '';
    if (!$token) return null;

    $price = null;

    // 1. Try cheapest prices endpoint
    $url = "https://api.travelpayouts.com/v1/prices/cheap?origin={$fromCode}&destination={$toCode}&currency=usd&token={$token}";
    $response = @file_get_contents($url);
    if ($response) {
        $data = json_decode($response, true);
        if (!empty($data['success']) && isset($data['data'][$toCode])) {
            $cheapest = null;
            foreach ($data['data'][$toCode] as $flight) {
                if (!$cheapest || $flight['price'] < $cheapest['price']) {
                    $cheapest = $flight;
                }
            }
            if ($cheapest) {
                $price = (int)$cheapest['price'];
            }
        }
    }

    // 2. Fallback: latest prices endpoint (broader coverage)
    if ($price === null) {
        $url2 = "https://api.travelpayouts.com/v2/prices/latest?origin={$fromCode}&destination={$toCode}&currency=usd&token={$token}&limit=5&sorting=price&trip_class=0";
        $resp2 = @file_get_contents($url2);
        if ($resp2) {
            $data2 = json_decode($resp2, true);
            if (!empty($data2['success']) && !empty($data2['data'])) {
                $price = (int)$data2['data'][0]['value'];
            }
        }
    }

    // 3. Fallback: direct flights endpoint
    if ($price === null) {
        $url3 = "https://api.travelpayouts.com/v1/prices/direct?origin={$fromCode}&destination={$toCode}&currency=usd&token={$token}";
        $resp3 = @file_get_contents($url3);
        if ($resp3) {
            $data3 = json_decode($resp3, true);
            if (!empty($data3['success']) && isset($data3['data'][$toCode])) {
                $price = (int)$data3['data'][$toCode]['price'];
            }
        }
    }

    if ($price !== null) {
        file_put_contents($cacheFile, json_encode(['price' => $price, 'updated' => date('Y-m-d H:i:s')]));
        return $price;
    }

    return null;
}

function getDestinationsWithLivePrices($pdo) {
    $destinations = getDestinations($pdo);
    foreach ($destinations as &$dest) {
        $livePrice = getDestinationFlightPrice($dest['name']);
        if ($livePrice !== null) {
            $dest['price'] = $livePrice;
        }
    }
    return $destinations;
}
