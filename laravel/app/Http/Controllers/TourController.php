<?php

namespace App\Http\Controllers;

use App\Models\Tour;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::active()->featured()->get();
        return view('tours.index', compact('tours'));
    }

    public function ingoing()
    {
        $tours = Tour::active()->byType('ingoing')->get();
        return view('tours.ingoing', compact('tours'));
    }

    public function outgoing()
    {
        $tours = Tour::active()->byType('outgoing')->get();
        return view('tours.outgoing', compact('tours'));
    }

    public function transfer()
    {
        $tours = Tour::active()->byType('transfer')->get();
        return view('tours.transfer', compact('tours'));
    }

    public function show(string $slug)
    {
        $tour = Tour::active()->where('slug', $slug)->firstOrFail();
        $relatedTours = Tour::active()
            ->where('type', $tour->type)
            ->where('id', '!=', $tour->id)
            ->when($tour->region, fn($q) => $q->orderByRaw("CASE WHEN region = ? THEN 0 ELSE 1 END", [$tour->region]))
            ->limit(3)
            ->get();
        return view('tours.show', compact('tour', 'relatedTours'));
    }
}
