<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function ($schedule) {
        // Crawl GitHub for steering docs every hour - stay fresh!
        $schedule->command('crawl:steering-docs')->hourly();
        
        // Clean up old failed jobs monthly
        $schedule->command('queue:prune-failed --hours=720')->monthly();
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        // Stripe posts webhook events with no CSRF token — this route is
        // instead protected by signature verification (Cashier's
        // VerifyWebhookSignature, applied automatically once
        // STRIPE_WEBHOOK_SECRET is set). Without this exclusion every real
        // webhook delivery 419s before it ever reaches the controller.
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // Removed AuthenticateSession middleware to fix "viaRemember" method not found error
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
