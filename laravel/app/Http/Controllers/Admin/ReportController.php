<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $monthlyRevenue = Booking::where('status', 'confirmed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $topDestinations = Booking::where('status', 'confirmed')
            ->select('destination', DB::raw('COUNT(*) as count'))
            ->groupBy('destination')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        return view('admin.reports', compact('monthlyRevenue', 'topDestinations'));
    }
}
