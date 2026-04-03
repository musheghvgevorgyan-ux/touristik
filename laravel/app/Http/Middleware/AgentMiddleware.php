<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'agent') {
            return redirect('/')->with('error', 'Access denied. Agent privileges required.');
        }

        return $next($request);
    }
}
