<?php

namespace App\Middleware;

use Core\App;
use Core\Request;

class CsrfMiddleware
{
    public function handle(Request $request): bool
    {
        if (!$request->isPost()) {
            return true;
        }

        $session = App::get('session');
        $token = $request->post('_csrf_token', '');

        if (!$session->verifyCsrf($token)) {
            if ($request->isAjax()) {
                App::get('response')->json(['error' => 'Invalid CSRF token.'], 403);
            } else {
                $session->flash('error', 'Form expired. Please try again.');
                App::get('response')->back();
            }
            return false;
        }

        // Regenerate token after successful verification
        $session->regenerateCsrf();
        return true;
    }
}
