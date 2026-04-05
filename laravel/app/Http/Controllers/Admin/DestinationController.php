<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::orderBy('name')->paginate(20);

        return view('admin.destinations', compact('destinations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'slug'        => 'nullable|string|max:200',
            'description' => 'nullable|string|max:5000',
            'country'     => 'nullable|string|max:100',
            'price_from'  => 'nullable|numeric|min:0',
            'image_url'   => 'nullable|url|max:500',
            'color'       => 'nullable|string|max:20',
            'emoji'       => 'nullable|string|max:10',
            'status'      => 'nullable|string|in:active,inactive,draft',
            'featured'    => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['featured'] = $request->has('featured');

        Destination::create($validated);

        return redirect('/admin/destinations')->with('success', 'Destination created.');
    }

    public function update(Request $request, $id)
    {
        $dest = Destination::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'slug'        => 'nullable|string|max:200',
            'description' => 'nullable|string|max:5000',
            'country'     => 'nullable|string|max:100',
            'price_from'  => 'nullable|numeric|min:0',
            'image_url'   => 'nullable|url|max:500',
            'color'       => 'nullable|string|max:20',
            'emoji'       => 'nullable|string|max:10',
            'status'      => 'nullable|string|in:active,inactive,draft',
            'featured'    => 'nullable',
        ]);

        $validated['featured'] = $request->has('featured');
        $dest->update($validated);

        return redirect('/admin/destinations')->with('success', 'Destination updated.');
    }

    public function delete($id)
    {
        Destination::findOrFail($id)->delete();
        return redirect('/admin/destinations')->with('success', 'Destination deleted.');
    }
}
