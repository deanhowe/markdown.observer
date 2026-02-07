<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ForgotPasswordPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/forgot-password';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Forgot your password?')
                ->assertPresent('@email')
                ->assertPresent('@submit-button');
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
            '@submit-button' => 'button[type="submit"]',
            '@login' => 'a[href*="login"]',
        ];
    }

    /**
     * Request a password reset link.
     */
    public function requestPasswordReset(Browser $browser, string $email): void
    {
        $browser->type('@email', $email)
                ->click('@submit-button')
                ->waitForText('We have emailed your password reset link');
    }
}
