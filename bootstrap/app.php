<?php

use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'legacy_badge' => \App\Http\Middleware\EnsureLegacyBadge::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'login',
            'logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->routeIs('login') || $request->is('login')) {
                return redirect()
                    ->route('login')
                    ->with('status', 'La session a expire. Reessaie de te connecter.');
            }

            return redirect()
                ->to(url()->previous() ?: route('home'))
                ->with('error', 'La session a expire. Recharge la page et reessaie.');
        });
    })->create();
