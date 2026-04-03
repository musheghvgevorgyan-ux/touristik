<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.bookings', compact('bookings'));
    }

    public function show(int $id)
    {
        $booking = Booking::with('user')->findOrFail($id);

        return view('admin.booking-detail', compact('booking'));
    }
}
