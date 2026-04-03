<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;

class CurrencyApiController extends Controller
{
    public function rates(CurrencyService $currencyService)
    {
        return response()->json([
            'rates'      => $currencyService->getRates(),
            'base'       => 'AMD',
            'updated_at' => $currencyService->getLastUpdated(),
        ]);
    }
}
