<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CallbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            Mail::send('emails.callback', [
                'callbackName' => $validated['name'],
                'callbackPhone' => $validated['phone'],
                'callbackNote' => $validated['note'] ?? '',
            ], function ($m) use ($validated) {
                $m->to('info@touristik.am')
                  ->subject('Callback Request: ' . $validated['name'] . ' - ' . $validated['phone']);
            });
        } catch (\Exception $e) {
            \Log::error('Callback email failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'We will call you shortly!']);
    }
}
