<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\User;
use App\Models\Tour;
use App\Models\Post;
use App\Models\Destination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Overview stats
        $stats = [
            'total_users' => User::count(),
            'new_users_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_bookings' => Booking::count(),
            'bookings_month' => Booking::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_contacts' => Contact::count(),
            'contacts_month' => Contact::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_tours' => Tour::count(),
            'total_destinations' => Destination::count(),
            'total_posts' => Post::published()->count(),
        ];

        // Monthly trends (last 12 months)
        $monthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthly[] = [
                'label' => $month->format('M Y'),
                'short' => $month->format('M'),
                'users' => User::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
                'bookings' => Booking::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
                'contacts' => Contact::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
            ];
        }

        // Recent contacts
        $recentContacts = Contact::orderByDesc('created_at')->take(10)->get();

        // Tours by type
        $toursByType = [
            'ingoing' => Tour::where('type', 'ingoing')->count(),
            'outgoing' => Tour::where('type', 'outgoing')->count(),
            'transfer' => Tour::where('type', 'transfer')->count(),
        ];

        return view('admin.reports', compact('stats', 'monthly', 'recentContacts', 'toursByType'));
    }
}
