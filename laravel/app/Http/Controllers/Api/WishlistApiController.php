<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistApiController extends Controller
{
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:hotel,tour,destination',
            'id'   => 'required|integer',
        ]);

        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('wishable_type', $validated['type'])
            ->where('wishable_id', $validated['id'])
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'status' => 'removed',
                'message' => 'Removed from wishlist.',
            ]);
        }

        Wishlist::create([
            'user_id'       => $request->user()->id,
            'wishable_type' => $validated['type'],
            'wishable_id'   => $validated['id'],
        ]);

        return response()->json([
            'status' => 'added',
            'message' => 'Added to wishlist.',
        ]);
    }
}
