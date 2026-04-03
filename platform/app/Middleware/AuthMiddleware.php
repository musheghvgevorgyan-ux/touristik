<?php

namespace App\Middleware;

use Core\App;
use Core\Request;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        $session = App::get('session');

        if (!$session->isLoggedIn()) {
            // Store intended URL for redirect after login
            $session->flash('intended_url', $request->uri());
            $session->flash('error', 'Please log in to continue.');
            App::get('response')->redirect('/login');
            return false;
        }

        // Check if user is suspended
        $user = \App\Models\User::find($session->userId());
        if (!$user || $user['status'] !== 'active') {
            $session->destroy();
            session_start();
            App::get('session')->flash('error', 'Your account has been suspended.');
            App::get('response')->redirect('/login');
            return false;
        }

        return true;
    }
}
