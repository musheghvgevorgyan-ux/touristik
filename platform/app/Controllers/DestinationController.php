<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Destination;

class DestinationController extends Controller
{
    /**
     * Show all active destinations
     */
    public function index(): void
    {
        $destinations = Destination::active();

        $this->view('destinations.index', [
            'title'        => 'Destinations — Touristik',
            'destinations' => $destinations,
        ]);
    }

    /**
     * Show a single destination by slug
     */
    public function show(string $slug): void
    {
        $destination = Destination::findBySlug($slug);

        if (!$destination) {
            $this->response->notFound('Destination not found');
            return;
        }

        $this->view('destinations.show', [
            'title'       => $destination['name'] . ' — Touristik',
            'destination' => $destination,
        ]);
    }
}
