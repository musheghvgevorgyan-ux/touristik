<?php

namespace App\Services;

class CurrencyService
{
    private static ?array $rates = null;

    /**
     * Get currency exchange rates (cached for 6 hours)
     */
    public static function getRates(): array
    {
        if (self::$rates !== null) {
            return self::$rates;
        }

        $cacheDir = BASE_PATH . '/cache';
        if (!is_dir($cacheDir)) mkdir($cacheDir, 0700, true);
        $cacheFile = $cacheDir . '/currency_rates.json';

        // Return cached rates if fresh (6 hours)
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 21600) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && isset($data['rates'])) {
                self::$rates = $data['rates'];
                return self::$rates;
            }
        }

        $defaults = ['USD' => 1, 'EUR' => 0.87, 'AMD' => 388, 'RUB' => 84];

        $ch = curl_init('https://open.er-api.com/v6/latest/USD');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            self::$rates = $defaults;
            return self::$rates;
        }

        $data = json_decode($response, true);
        if (empty($data['rates'])) {
            self::$rates = $defaults;
            return self::$rates;
        }

        self::$rates = [
            'USD' => 1,
            'EUR' => round($data['rates']['EUR'] ?? 0.87, 4),
            'AMD' => round($data['rates']['AMD'] ?? 388, 2),
            'RUB' => round($data['rates']['RUB'] ?? 84, 2),
        ];

        $result = json_encode([
            'rates'   => self::$rates,
            'source'  => 'open.er-api.com',
            'updated' => date('Y-m-d H:i:s'),
        ]);
        file_put_contents($cacheFile, $result);

        return self::$rates;
    }
}
