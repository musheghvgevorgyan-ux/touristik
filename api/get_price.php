<?php
header('Content-Type: application/json');

$config = json_decode(file_get_contents(__DIR__ . '/../config/travelpayouts.json'), true);
$token = $config['api_token'];

// IATA codes mapping
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

$from = isset($_GET['from']) ? trim($_GET['from']) : '';
$to = isset($_GET['to']) ? trim($_GET['to']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : 'roundtrip';
$depart = isset($_GET['depart']) ? trim($_GET['depart']) : '';
$returnDate = isset($_GET['return']) ? trim($_GET['return']) : '';

if (!$from || !$to) {
    echo json_encode(['found' => false]);
    exit;
}

$fromCode = $iataCodes[$from] ?? '';
$toCode = $iataCodes[$to] ?? '';

if (!$fromCode || !$toCode) {
    echo json_encode(['found' => false, 'error' => 'Unknown city']);
    exit;
}

// Cache file to avoid hitting API too often (cache for 1 hour)
$cacheDir = __DIR__ . '/../cache';
if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);
$cacheKey = md5($fromCode . $toCode . $type . $depart);
$cacheFile = $cacheDir . '/' . $cacheKey . '.json';

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
    echo file_get_contents($cacheFile);
    exit;
}

// Try Travelpayouts "prices for dates" endpoint
$result = null;

// 1. Try specific date search if date provided
if ($depart) {
    $month = substr($depart, 0, 7); // YYYY-MM
    $url = "https://api.travelpayouts.com/v1/prices/cheap?origin={$fromCode}&destination={$toCode}&depart_date={$month}&currency=usd&token={$token}";
    $response = @file_get_contents($url);
    if ($response) {
        $data = json_decode($response, true);
        if ($data['success'] && isset($data['data'][$toCode])) {
            $flights = $data['data'][$toCode];
            // Find cheapest
            $cheapest = null;
            foreach ($flights as $flight) {
                if (!$cheapest || $flight['price'] < $cheapest['price']) {
                    $cheapest = $flight;
                }
            }
            if ($cheapest) {
                $price = $cheapest['price'];
                if ($type === 'oneway') {
                    $price = round($price / 2);
                }
                $result = [
                    'found' => true,
                    'price' => $price,
                    'airline' => $cheapest['airline'] ?? '',
                    'departure_at' => $cheapest['departure_at'] ?? '',
                    'return_at' => $cheapest['return_at'] ?? '',
                    'expires_at' => $cheapest['expires_at'] ?? '',
                    'source' => 'travelpayouts'
                ];
            }
        }
    }
}

// 2. Fallback: latest prices (no date)
if (!$result) {
    $url = "https://api.travelpayouts.com/v1/prices/cheap?origin={$fromCode}&destination={$toCode}&currency=usd&token={$token}";
    $response = @file_get_contents($url);
    if ($response) {
        $data = json_decode($response, true);
        if ($data['success'] && isset($data['data'][$toCode])) {
            $flights = $data['data'][$toCode];
            $cheapest = null;
            foreach ($flights as $flight) {
                if (!$cheapest || $flight['price'] < $cheapest['price']) {
                    $cheapest = $flight;
                }
            }
            if ($cheapest) {
                $price = $cheapest['price'];
                if ($type === 'oneway') {
                    $price = round($price / 2);
                }
                $result = [
                    'found' => true,
                    'price' => $price,
                    'airline' => $cheapest['airline'] ?? '',
                    'departure_at' => $cheapest['departure_at'] ?? '',
                    'return_at' => $cheapest['return_at'] ?? '',
                    'expires_at' => $cheapest['expires_at'] ?? '',
                    'source' => 'travelpayouts'
                ];
            }
        }
    }
}

// 3. Fallback: local DB prices
if (!$result) {
    require_once __DIR__ . '/../includes/db.php';
    $stmt = $pdo->prepare("SELECT price FROM flight_prices WHERE from_city = ? AND to_city = ? AND trip_type = ?");
    $stmt->execute([$from, $to, $type]);
    $row = $stmt->fetch();
    if (!$row) {
        $stmt->execute([$to, $from, $type]);
        $row = $stmt->fetch();
    }
    if ($row) {
        $result = ['found' => true, 'price' => (float)$row['price'], 'source' => 'local'];
    }
}

if (!$result) {
    $result = ['found' => false];
}

// Cache the result
file_put_contents($cacheFile, json_encode($result));
echo json_encode($result);
