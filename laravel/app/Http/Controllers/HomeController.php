<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Tour;
use App\Services\SettingsService;

class HomeController extends Controller
{
    public function index()
    {
        $destinations = Destination::where('featured', true)->take(6)->get();
        $tours = Tour::where('featured', true)->take(4)->get();
        $heroTitle = SettingsService::get('hero_title', 'Explore the World with Touristik');
        $heroSubtitle = SettingsService::get('hero_subtitle', 'Discover breathtaking destinations and create unforgettable memories');

        return view('home.index', compact('destinations', 'tours', 'heroTitle', 'heroSubtitle'));
    }

    public function about()
    {
        return view('home.about');
    }
}
