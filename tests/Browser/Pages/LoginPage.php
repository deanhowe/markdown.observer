<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class LoginPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/login';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Log in to your account')
                ->assertPresent('@email')
                ->assertPresent('@password')
                ->assertPresent('@remember')
                ->assertPresent('@login-button');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@email' => 'input[type="email"]',
            '@password' => 'input[type="password"]',
            '@remember' => 'input[name="remember"]',
            '@login-button' => 'button[type="submit"]',
            '@forgot-password' => 'a[href*="forgot-password"]',
            '@register' => 'a[href*="register"]',
        ];
    }

    /**
     * Login with the given credentials.
     */
    public function login(Browser $browser, string $email, string $password, bool $remember = false): void
    {
        $browser->type('@email', $email)
                ->type('@password', $password);

        if ($remember) {
            $browser->click('@remember');
        }

        $browser->click('@login-button')
                ->waitForLocation('/dashboard');
    }
}
