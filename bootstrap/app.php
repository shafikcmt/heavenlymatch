<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // SetLocale must run after StartSession + EncryptCookies (both in the core web group)
        // so it can read the session and decrypted cookie set by switchLocale.
        // It must also run before HandleInertiaRequests so App::getLocale() is correct
        // when Inertia shares the locale + translations props.
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Named middleware aliases
        $middleware->alias([
            'verified.user' => \App\Http\Middleware\EnsureUserIsVerified::class,
            'check.biodata' => \App\Http\Middleware\CheckBiodataCompletion::class,
            'admin'         => \App\Http\Middleware\EnsureAdmin::class,
            'set.locale'    => \App\Http\Middleware\SetLocale::class,
        ]);

        // Throttle API routes
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($request->inertia()) {
                return Inertia::render('Errors/HttpError', [
                    'status' => $e->getStatusCode(),
                ])
                ->toResponse($request)
                ->setStatusCode($e->getStatusCode());
            }
        });
    })
    ->create();
