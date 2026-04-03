<?php

namespace App\Http\Controllers;

use App\Models\Destination;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::orderBy('name')->get();

        return view('destinations.index', compact('destinations'));
    }

    public function show(string $slug)
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();

        return view('destinations.show', compact('destination'));
    }
}
