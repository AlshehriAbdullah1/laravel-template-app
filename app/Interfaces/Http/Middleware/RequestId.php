<?php

namespace App\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RequestId
{
    public function handle($request, Closure $next)
    {
        $id = $request->headers->get('X-Request-Id', (string) Str::ulid());
        Log::withContext(['request_id' => $id]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $id);
        return $response;
    }
}
