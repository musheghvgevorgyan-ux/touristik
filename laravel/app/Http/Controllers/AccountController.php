<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $bookingsCount = Booking::where('user_id', $user->id)->count();
        $recentBookings = Booking::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('account.dashboard', compact('user', 'bookingsCount', 'recentBookings'));
    }

    public function profile()
    {
        return view('account.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect('/account/profile')->with('success', 'Profile updated successfully.');
    }

    public function bookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('account.bookings', compact('bookings'));
    }
}
