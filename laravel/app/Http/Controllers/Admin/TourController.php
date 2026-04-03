<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::orderBy('sort_order')->paginate(20);

        return view('admin.tours', compact('tours'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'type'        => 'required|in:ingoing,outgoing,transfer',
            'description' => 'nullable|string|max:5000',
            'price'       => 'required|numeric|min:0',
            'duration'    => 'nullable|string|max:100',
            'image'       => 'nullable|image|max:2048',
            'active'      => 'boolean',
            'featured'    => 'boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('tours', 'public');
        }

        Tour::create($validated);

        return redirect('/admin/tours')->with('success', 'Tour created successfully.');
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:tours,id',
        ]);

        Tour::findOrFail($validated['id'])->delete();

        return redirect('/admin/tours')->with('success', 'Tour deleted successfully.');
    }
}
