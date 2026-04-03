<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_bookings' => Booking::where('agent_id', $user->id)->count(),
            'month_bookings' => Booking::where('agent_id', $user->id)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'total_commission' => Booking::where('agent_id', $user->id)
                ->where('status', 'confirmed')
                ->sum('commission'),
        ];

        $recentBookings = Booking::where('agent_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('agent.dashboard', compact('stats', 'recentBookings'));
    }
}
