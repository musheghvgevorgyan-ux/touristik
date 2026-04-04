<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $sent = $request->get('sent');
        return view('contact.index', ['sent' => $sent]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:255',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:10|max:5000',
        ]);

        Contact::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'user_id' => auth()->id(),
            'status'  => 'new',
        ]);

        try {
            Mail::raw(
                "New contact form submission:\n\n" .
                "Name: {$validated['name']}\n" .
                "Email: {$validated['email']}\n" .
                "Subject: " . ($validated['subject'] ?? 'N/A') . "\n\n" .
                "Message:\n{$validated['message']}",
                function ($m) use ($validated) {
                    $m->to('info@touristik.am')
                      ->replyTo($validated['email'], $validated['name'])
                      ->subject('New Contact: ' . ($validated['subject'] ?? 'Website Inquiry'));
                }
            );
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Contact email failed: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Thank you! Your message has been sent.']);
        }
        return redirect('/contact?sent=1');
    }
}
