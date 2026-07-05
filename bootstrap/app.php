<?php

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
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // SSLCommerz posts back from its own domain — no CSRF token there.
        // Payment truth is established by server-side validation instead.
        $middleware->validateCsrfTokens(except: [
            'payment/success',
            'payment/fail',
            'payment/cancel',
            'payment/ipn',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A POST beyond post_max_size never reaches validation — turn the
        // bare 413 into a message on the form the admin actually sees.
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, \Illuminate\Http\Request $request) {
            return back()->with(
                'error',
                'The upload is larger than the server accepts in one request. Use smaller files or raise post_max_size.'
            );
        });
    })->create();
