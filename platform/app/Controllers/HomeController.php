<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Destination;
use App\Services\SettingsService;

class HomeController extends Controller
{
    public function index(): void
    {
        $destinations = Destination::featured(6);
        $heroTitle = SettingsService::get('hero_title', 'Explore the World with Touristik');
        $heroSubtitle = SettingsService::get('hero_subtitle', 'Discover breathtaking destinations and create unforgettable memories');

        $this->view('home.index', [
            'title'        => 'Touristik — Your Journey, Our Passion',
            'destinations' => $destinations,
            'heroTitle'    => $heroTitle,
            'heroSubtitle' => $heroSubtitle,
        ]);
    }

    public function about(): void
    {
        $this->view('home.about', [
            'title' => 'About Us — Touristik',
        ]);
    }
}
