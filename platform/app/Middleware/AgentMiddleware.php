<?php

namespace App\Middleware;

use Core\App;
use Core\Request;

class AgentMiddleware
{
    public function handle(Request $request): bool
    {
        $session = App::get('session');
        $role = $session->userRole();

        if ($role !== 'agent') {
            App::get('session')->flash('error', 'Access denied. Agent account required.');
            App::get('response')->redirect('/');
            return false;
        }

        return true;
    }
}
