<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderByDesc('created_at')->paginate(20);

        return view('admin.contacts', compact('contacts'));
    }

    public function markRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->status = $contact->status === 'new' ? 'read' : 'new';
        $contact->save();

        return redirect('/admin/contacts')->with('success', 'Status updated.');
    }
}
