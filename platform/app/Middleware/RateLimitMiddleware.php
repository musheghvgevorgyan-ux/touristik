<?php

namespace App\Middleware;

use Core\App;
use Core\Request;

class RateLimitMiddleware
{
    private int $maxAttempts;
    private int $windowSeconds;

    public function __construct(int $maxAttempts = 5, int $windowSeconds = 900)
    {
        $this->maxAttempts = $maxAttempts;
        $this->windowSeconds = $windowSeconds;
    }

    public function handle(Request $request): bool
    {
        $session = App::get('session');
        $key = '_rate_limit_' . md5($request->uri() . $request->ip());

        $attempts = $session->get($key, ['count' => 0, 'first_at' => time()]);

        // Reset if window has passed
        if (time() - $attempts['first_at'] > $this->windowSeconds) {
            $attempts = ['count' => 0, 'first_at' => time()];
        }

        if ($attempts['count'] >= $this->maxAttempts) {
            $retryAfter = $this->windowSeconds - (time() - $attempts['first_at']);

            if ($request->isAjax()) {
                App::get('response')->json([
                    'error' => 'Too many attempts. Try again later.',
                    'retry_after' => $retryAfter,
                ], 429);
            } else {
                $session->flash('error', 'Too many attempts. Please wait ' . ceil($retryAfter / 60) . ' minutes.');
                App::get('response')->back();
            }
            return false;
        }

        $attempts['count']++;
        $session->set($key, $attempts);

        return true;
    }
}
