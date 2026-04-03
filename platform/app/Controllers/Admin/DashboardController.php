<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Booking;
use App\Models\User;
use Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();

        $totalUsers = User::count();
        $totalBookings = Booking::count();
        $recentBookings = $db->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10")->fetchAll();

        $this->view('admin.dashboard', [
            'title'          => 'Admin Dashboard — Touristik',
            'totalUsers'     => $totalUsers,
            'totalBookings'  => $totalBookings,
            'recentBookings' => $recentBookings,
        ]);
    }
}
