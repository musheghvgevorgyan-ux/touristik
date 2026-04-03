<?php

namespace App\Controllers\Api;

use Core\Controller;
use App\Services\CurrencyService;

/**
 * API controller for currency exchange rates.
 */
class CurrencyApiController extends Controller
{
    /**
     * GET /api/currency/rates
     *
     * Returns the current exchange rates as JSON.
     *
     * Response:
     * {
     *   "success": true,
     *   "base": "USD",
     *   "rates": { "USD": 1, "EUR": 0.87, "AMD": 388, "RUB": 84 }
     * }
     */
    public function rates(): void
    {
        try {
            $rates = CurrencyService::getRates();

            $this->json([
                'success' => true,
                'base'    => 'USD',
                'rates'   => $rates,
            ]);
        } catch (\Throwable $e) {
            error_log('CurrencyApiController::rates error: ' . $e->getMessage());

            $this->json([
                'success' => false,
                'error'   => 'Could not retrieve exchange rates.',
                'rates'   => ['USD' => 1, 'EUR' => 0.87, 'AMD' => 388, 'RUB' => 84],
            ], 500);
        }
    }
}
