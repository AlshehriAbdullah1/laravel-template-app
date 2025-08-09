<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;               
use Illuminate\Support\Facades\RateLimiter;  
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // 2) RATE LIMITING
            RateLimiter::for('api', function ($request) {
                $key = optional($request->user())->id ?: $request->ip();
                return Limit::perMinute(120)->by($key);
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware (applies to every request)
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,                         // built-in CORS
            \App\Interfaces\Http\Middleware\ForceJsonResponse::class,             // force JSON
            \App\Interfaces\Http\Middleware\RequestId::class,                     // X-Request-Id
        ]);

        // Route middleware aliases
        $aliases = [
            // Only include this if you actually created the class
            // 'idempotency' => \App\Interfaces\Http\Middleware\Idempotency::class,
            // Spatie Permission (note: Middlewares, plural)
            'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        ];
         $middleware->alias($aliases);

        // API group (what runs for routes in routes/api.php)
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // If you use Sanctum SPA cookie mode, also add:
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
     ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $r) {
            return response()->json(['type'=>'about:blank','title'=>'Unauthenticated','status'=>401], 401);
        });
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $r) {
            return response()->json(['type'=>'about:blank','title'=>'Forbidden','status'=>403], 403);
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $r) {
            return response()->json(['type'=>'about:blank','title'=>'Not Found','status'=>404], 404);
        });
    })->create();
