<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', Auth::id())
            ->with('wishable')
            ->latest()
            ->paginate(12);

        return view('account.wishlist', compact('items'));
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:hotel,tour,destination',
            'id'   => 'required|integer',
        ]);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('wishable_type', $validated['type'])
            ->where('wishable_id', $validated['id'])
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Removed from wishlist.');
        }

        Wishlist::create([
            'user_id'       => Auth::id(),
            'wishable_type' => $validated['type'],
            'wishable_id'   => $validated['id'],
        ]);

        return back()->with('success', 'Added to wishlist.');
    }
}
