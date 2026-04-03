<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HotelbedsAdapter;
use Illuminate\Http\Request;

class HotelApiController extends Controller
{
    public function search(Request $request, HotelbedsAdapter $hotelbeds)
    {
        $validated = $request->validate([
            'destination' => 'required|string',
            'check_in'    => 'required|date|after_or_equal:today',
            'check_out'   => 'required|date|after:check_in',
            'adults'      => 'required|integer|min:1|max:9',
            'children'    => 'nullable|integer|min:0|max:6',
        ]);

        $results = $hotelbeds->searchHotels($validated);

        return response()->json([
            'results' => $results,
            'filters' => $validated,
        ]);
    }
}
