<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Currency exchange rate service.
 *
 * Fetches rates from the open.er-api.com API and caches them for 6 hours
 * via Laravel's Cache facade. Falls back to hardcoded defaults on failure.
 */
class CurrencyService
{
    private const CACHE_KEY = 'currency_exchange_rates';
    private const CACHE_TTL = 21600; // 6 hours in seconds

    /**
     * Default fallback rates (base: USD).
     */
    private static array $defaults = [
        'USD' => 1,
        'EUR' => 0.87,
        'AMD' => 388,
        'RUB' => 84,
    ];

    /**
     * Get currency exchange rates (cached for 6 hours).
     *
     * @return array ['USD' => 1, 'EUR' => 0.87, 'AMD' => 388, 'RUB' => 84]
     */
    public static function getRates(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            try {
                $response = Http::timeout(10)->get('https://open.er-api.com/v6/latest/USD');

                if (!$response->successful()) {
                    Log::warning('CurrencyService: API returned non-200 status', [
                        'status' => $response->status(),
                    ]);
                    return self::$defaults;
                }

                $data = $response->json();

                if (empty($data['rates'])) {
                    return self::$defaults;
                }

                return [
                    'USD' => 1,
                    'EUR' => round($data['rates']['EUR'] ?? 0.87, 4),
                    'AMD' => round($data['rates']['AMD'] ?? 388, 2),
                    'RUB' => round($data['rates']['RUB'] ?? 84, 2),
                ];
            } catch (\Throwable $e) {
                Log::warning('CurrencyService: Failed to fetch rates', [
                    'error' => $e->getMessage(),
                ]);
                return self::$defaults;
            }
        });
    }
}
