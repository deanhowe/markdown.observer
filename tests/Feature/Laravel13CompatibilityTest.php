<?php

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\TokenMismatchException;

test('main and AI hosts retain distinct home routes', function () {
    foreach (['https://markdown.observer.test/' => 'home', 'https://ai.markdown.observer.test/' => 'ai.home'] as $url => $name) {
        expect(Route::getRoutes()->match(Request::create($url))->getName())->toBe($name);
    }
});

test('Stripe webhook remains exempt while cross-site checkout requires a token', function () {
    $middleware = new class(app(), app(Encrypter::class)) extends PreventRequestForgery {
        protected $addHttpCookie = false;

        protected function runningUnitTests()
        {
            return false;
        }
    };

    $webhook = Request::create('https://markdown.observer.test/stripe/webhook', 'POST');
    $webhook->headers->set('Sec-Fetch-Site', 'cross-site');
    $webhook->setLaravelSession(app('session')->driver());
    expect($middleware->handle($webhook, fn () => response('accepted'))->getContent())->toBe('accepted');

    $checkout = Request::create('https://markdown.observer.test/checkout/pro-monthly', 'POST');
    $checkout->headers->set('Sec-Fetch-Site', 'cross-site');
    $checkout->setLaravelSession(app('session')->driver());
    expect(fn () => $middleware->handle($checkout, fn () => response('unexpected')))
        ->toThrow(TokenMismatchException::class);
});
