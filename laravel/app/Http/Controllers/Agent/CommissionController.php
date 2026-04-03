<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $commissions = Booking::where('agent_id', $user->id)
            ->where('status', 'confirmed')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(commission) as total'),
                DB::raw('COUNT(*) as bookings')
            )
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(12);

        $totalCommission = Booking::where('agent_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('commission');

        return view('agent.commission', compact('commissions', 'totalCommission'));
    }
}
