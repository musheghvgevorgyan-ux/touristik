<?php
function getCurrencyRates() {
    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) mkdir($cacheDir, 0700, true);
    $cacheFile = $cacheDir . '/currency_rates.json';

    // Return cached rates if fresh (6 hours)
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 21600) {
        $data = json_decode(file_get_contents($cacheFile), true);
        if ($data && isset($data['rates'])) {
            return $data['rates'];
        }
    }

    $defaults = ['USD' => 1, 'EUR' => 0.87, 'AMD' => 388, 'RUB' => 84];

    // Fetch live rates from open.er-api.com (free, includes AMD)
    $ch = curl_init('https://open.er-api.com/v6/latest/USD');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return $defaults;

    $data = json_decode($response, true);
    if (empty($data['rates'])) return $defaults;

    $rates = [
        'USD' => 1,
        'EUR' => round($data['rates']['EUR'] ?? 0.87, 4),
        'AMD' => round($data['rates']['AMD'] ?? 388, 2),
        'RUB' => round($data['rates']['RUB'] ?? 84, 2)
    ];

    // Cache result
    $result = json_encode(['rates' => $rates, 'source' => 'open.er-api.com', 'updated' => date('Y-m-d H:i:s')]);
    file_put_contents($cacheFile, $result);

    return $rates;
}
