<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function myReviews()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('account.reviews', compact('reviews'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'   => 'required|in:hotel,tour,destination',
            'id'     => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:200',
            'body'   => 'required|string|max:2000',
        ]);

        Review::create([
            'user_id'         => Auth::id(),
            'reviewable_type' => $validated['type'],
            'reviewable_id'   => $validated['id'],
            'rating'          => $validated['rating'],
            'title'           => $validated['title'] ?? null,
            'body'            => $validated['body'],
            'status'          => 'pending',
        ]);

        return back()->with('success', 'Review submitted. It will appear after moderation.');
    }
}
