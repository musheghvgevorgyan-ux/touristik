<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Post;
use App\Models\Tour;
use App\Models\Destination;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBookings = Booking::count();
        $newMessages = Contact::where('status', 'new')->count();
        $totalTours = Tour::count();
        $totalDestinations = Destination::count();
        $totalPosts = Post::published()->count();
        $recentBookings = Booking::latest()->take(10)->get();
        $recentContacts = Contact::orderByDesc('created_at')->take(5)->get();

        // Monthly stats for chart (last 6 months)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyStats[] = [
                'label' => $month->format('M'),
                'bookings' => Booking::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
                'users' => User::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'totalBookings', 'newMessages', 'totalTours',
            'totalDestinations', 'totalPosts', 'recentBookings', 'recentContacts', 'monthlyStats'
        ));
    }
}
