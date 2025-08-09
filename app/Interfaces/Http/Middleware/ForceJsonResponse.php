<?php

namespace App\Interfaces\Http\Middleware;

use Closure;

class ForceJsonResponse
{
    public function handle($request, Closure $next)
    {
        // Ensure every request asks for JSON
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
