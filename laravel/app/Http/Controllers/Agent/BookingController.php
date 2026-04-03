<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('agent_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('agent.bookings', compact('bookings'));
    }

    public function show(int $id)
    {
        $booking = Booking::where('agent_id', Auth::id())
            ->findOrFail($id);

        return view('agent.bookings.show', compact('booking'));
    }
}
