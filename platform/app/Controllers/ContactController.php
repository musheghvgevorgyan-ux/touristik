<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Contact;
use App\Helpers\Flash;
use App\Helpers\Redirect;

class ContactController extends Controller
{
    /**
     * Show the contact form
     */
    public function index(): void
    {
        $this->view('contact.index', [
            'title' => 'Contact Us — Touristik',
        ]);
    }

    /**
     * Handle contact form submission
     */
    public function send(): void
    {
        $errors = $this->validate([
            'name'    => 'required',
            'email'   => 'required|email',
            'message' => 'required|min:10',
        ]);

        if (!empty($errors)) {
            Redirect::withErrors($errors, '/contact');
            return;
        }

        Contact::create([
            'name'       => $this->request->post('name'),
            'email'      => $this->request->post('email'),
            'phone'      => $this->request->post('phone', ''),
            'subject'    => $this->request->post('subject', ''),
            'message'    => $this->request->post('message'),
            'ip_address' => $this->request->ip(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Flash::success('Thank you for your message! We will get back to you shortly.');
        $this->redirect('/contact');
    }
}
