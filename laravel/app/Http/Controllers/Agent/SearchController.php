<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\HotelbedsAdapter;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index()
    {
        return view('agent.search');
    }

    public function results(Request $request, HotelbedsAdapter $hotelbeds)
    {
        $validated = $request->validate([
            'destination' => 'required|string',
            'check_in'    => 'required|date|after_or_equal:today',
            'check_out'   => 'required|date|after:check_in',
            'adults'      => 'required|integer|min:1|max:9',
            'children'    => 'nullable|integer|min:0|max:6',
        ]);

        $results = $hotelbeds->searchHotels($validated);

        return view('agent.search-results', [
            'results' => $results,
            'filters' => $validated,
        ]);
    }
}
