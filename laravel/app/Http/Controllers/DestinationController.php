<?php

namespace App\Http\Controllers;

use App\Models\Destination;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::orderBy('name')->get();

        $mapData = $destinations->map(function ($d) {
            return [
                'name' => $d->name,
                'slug' => $d->slug,
                'country' => $d->country ?? '',
                'price' => $d->price_from ?? 0,
                'image' => $d->image_url ?? '',
                'lat' => $d->latitude,
                'lng' => $d->longitude,
            ];
        });

        return view('destinations.index', compact('destinations', 'mapData'));
    }

    public function show(string $slug)
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();

        return view('destinations.show', compact('destination'));
    }
}
