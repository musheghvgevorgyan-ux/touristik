<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

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

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Thank you! Your message has been sent.']);
        }
        return redirect('/contact?sent=1');
    }
}
