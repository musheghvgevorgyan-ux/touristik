<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Tour;

class TourController extends Controller
{
    /**
     * Tours landing page — show featured tours from DB
     */
    public function index(): void
    {
        $tours = Tour::featured(6);

        $this->view('tours.index', [
            'title' => 'Tours — Touristik',
            'tours' => $tours,
        ]);
    }

    /**
     * Ingoing tours page
     */
    public function ingoing(): void
    {
        $tours = Tour::byType('ingoing');

        $this->view('tours.ingoing', [
            'title' => 'Ingoing Tours — Touristik',
            'tours' => $tours,
        ]);
    }

    /**
     * Outgoing tours page
     */
    public function outgoing(): void
    {
        $tours = Tour::byType('outgoing');

        $this->view('tours.outgoing', [
            'title' => 'Outgoing Tours — Touristik',
            'tours' => $tours,
        ]);
    }

    /**
     * Transfer page
     */
    public function transfer(): void
    {
        $tours = Tour::byType('transfer');

        $this->view('tours.transfer', [
            'title' => 'Transfers — Touristik',
            'tours' => $tours,
        ]);
    }

    /**
     * Single tour detail page
     */
    public function show(string $slug): void
    {
        $tour = Tour::findBySlug($slug);

        if (!$tour || $tour['status'] !== 'active') {
            $this->response->notFound('Tour not found');
            return;
        }

        $this->view('tours.show', [
            'title' => $tour['title'] . ' — Touristik',
            'tour'  => $tour,
        ]);
    }
}
