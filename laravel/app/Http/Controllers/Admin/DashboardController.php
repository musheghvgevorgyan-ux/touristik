<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBookings = Booking::count();
        $recentBookings = Booking::latest()->take(10)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalBookings', 'recentBookings'));
    }
}
