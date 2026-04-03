<?php

namespace App\Middleware;

use Core\App;
use Core\Request;

class AdminMiddleware
{
    public function handle(Request $request): bool
    {
        $session = App::get('session');
        $role = $session->userRole();

        if (!in_array($role, ['admin', 'superadmin'])) {
            App::get('session')->flash('error', 'Access denied.');
            App::get('response')->redirect('/');
            return false;
        }

        return true;
    }
}
