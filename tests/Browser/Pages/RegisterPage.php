<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class RegisterPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/register';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Create an account')
                ->assertPresent('@name')
                ->assertPresent('@email')
                ->assertPresent('@password')
                ->assertPresent('@password_confirmation')
                ->assertPresent('@register-button');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@name' => 'input[name="name"]',
            '@email' => 'input[type="email"]',
            '@password' => 'input[name="password"]',
            '@password_confirmation' => 'input[name="password_confirmation"]',
            '@register-button' => 'button[type="submit"]',
            '@login' => 'a[href*="login"]',
        ];
    }

    /**
     * Register a new user.
     */
    public function register(Browser $browser, string $name, string $email, string $password): void
    {
        $browser->type('@name', $name)
                ->type('@email', $email)
                ->type('@password', $password)
                ->type('@password_confirmation', $password)
                ->click('@register-button')
                ->waitForLocation('/dashboard');
    }
}
