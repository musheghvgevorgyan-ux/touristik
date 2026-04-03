<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.reviews', compact('reviews'));
    }

    public function moderate(Request $request, int $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $review->update(['status' => $validated['status']]);

        return redirect('/admin/reviews')->with('success', 'Review has been ' . $validated['status'] . '.');
    }
}
