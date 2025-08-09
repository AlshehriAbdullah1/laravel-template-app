<?php

namespace App\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class Idempotency
{
    public function handle($request, Closure $next)
    {
        if ($request->method() !== 'POST') {
            return $next($request);
        }

        $key = $request->header('Idempotency-Key');
        if (! $key) {
            return $next($request);
        }

        $cacheKey = 'idem:' . $key;
        if ($cached = Cache::get($cacheKey)) {
            return response()->json($cached['body'], $cached['status'], $cached['headers']);
        }

        $response = $next($request);

        Cache::put($cacheKey, [
            'status'  => $response->getStatusCode(),
            'headers' => ['Content-Type' => $response->headers->get('Content-Type')],
            'body'    => $response->getData(true),
        ], now()->addMinutes(10));

        return $response;
    }
}
