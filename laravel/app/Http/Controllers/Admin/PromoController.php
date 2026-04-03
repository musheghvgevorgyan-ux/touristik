<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::latest()->paginate(20);

        return view('admin.promos', compact('promos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:promos,code',
            'description' => 'nullable|string|max:500',
            'discount'    => 'required|numeric|min:0|max:100',
            'type'        => 'required|in:percent,fixed',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after:starts_at',
            'max_uses'    => 'nullable|integer|min:1',
        ]);

        Promo::create($validated);

        return redirect('/admin/promos')->with('success', 'Promo code created successfully.');
    }
}
