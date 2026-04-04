<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $typeFilter = $request->query('type');
        $tours = Tour::query()
            ->when($typeFilter, fn($q) => $q->where('type', $typeFilter))
            ->orderByDesc('created_at')
            ->paginate(20);

        $destinations = Destination::orderBy('name')->get(['id', 'name']);
        $title = 'Manage Tours';

        return view('admin.tours', compact('tours', 'destinations', 'typeFilter', 'title'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:200',
            'slug'           => 'nullable|string|max:200',
            'type'           => 'required|in:ingoing,outgoing,transfer',
            'region'         => 'nullable|string|max:50',
            'description'    => 'nullable|string|max:10000',
            'duration'       => 'nullable|string|max:100',
            'price_from'     => 'nullable|numeric|min:0',
            'image_url'      => 'nullable|string|max:500',
            'destination_id' => 'nullable|integer|exists:destinations,id',
            'status'         => 'required|in:active,inactive',
            'featured'       => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['featured'] = $request->has('featured');
        $validated['destination_id'] = $validated['destination_id'] ?: null;
        $validated['itinerary'] = $this->parseItinerary($request);
        $validated['gallery'] = $this->parseList($request, 'gallery_url');
        $validated['includes'] = $this->parseList($request, 'includes_item');
        $validated['excludes'] = $this->parseList($request, 'excludes_item');

        Tour::create($validated);

        return redirect('/admin/tours')->with('success', 'Tour created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $tour = Tour::findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:200',
            'slug'           => 'nullable|string|max:200',
            'type'           => 'required|in:ingoing,outgoing,transfer',
            'region'         => 'nullable|string|max:50',
            'description'    => 'nullable|string|max:10000',
            'duration'       => 'nullable|string|max:100',
            'price_from'     => 'nullable|numeric|min:0',
            'image_url'      => 'nullable|string|max:500',
            'destination_id' => 'nullable|integer|exists:destinations,id',
            'status'         => 'required|in:active,inactive',
            'featured'       => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['featured'] = $request->has('featured');
        $validated['destination_id'] = $validated['destination_id'] ?: null;
        $validated['itinerary'] = $this->parseItinerary($request);
        $validated['gallery'] = $this->parseList($request, 'gallery_url');
        $validated['includes'] = $this->parseList($request, 'includes_item');
        $validated['excludes'] = $this->parseList($request, 'excludes_item');

        $tour->update($validated);

        return redirect('/admin/tours')->with('success', 'Tour updated successfully.');
    }

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:tours,id']);
        Tour::findOrFail($request->id)->delete();
        return redirect('/admin/tours')->with('success', 'Tour deleted.');
    }

    private function parseItinerary(Request $request): ?array
    {
        $titles = $request->input('itinerary_title', []);
        $descs = $request->input('itinerary_desc', []);
        $items = [];
        foreach ($titles as $i => $title) {
            $title = trim($title);
            $desc = trim($descs[$i] ?? '');
            if ($title || $desc) {
                $items[] = ['title' => $title, 'description' => $desc];
            }
        }
        return $items ?: null;
    }

    private function parseList(Request $request, string $field): ?array
    {
        $items = array_filter(array_map('trim', $request->input($field, [])));
        return $items ? array_values($items) : null;
    }
}
