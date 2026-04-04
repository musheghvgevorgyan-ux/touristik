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
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'phone'      => 'nullable|string|max:30',
            'language'   => 'nullable|string|in:en,ru,hy',
            'currency'   => 'nullable|string|in:USD,EUR,AMD,RUB',
        ]);

        $user->name = $validated['first_name'] . ' ' . $validated['last_name'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->language = $validated['language'] ?? $user->language;
        $user->currency = $validated['currency'] ?? $user->currency;
        $user->save();

        return redirect('/account/profile')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect('/account/profile')->with('success', 'Password updated successfully.');
    }

    public function bookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('account.bookings', compact('bookings'));
    }
}
