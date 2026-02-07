<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class PasswordSettingsPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/settings/password';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Update Password')
                ->assertPresent('@current_password')
                ->assertPresent('@password')
                ->assertPresent('@password_confirmation')
                ->assertPresent('@save-button');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@current_password' => 'input[name="current_password"]',
            '@password' => 'input[name="password"]',
            '@password_confirmation' => 'input[name="password_confirmation"]',
            '@save-button' => 'button[type="submit"]',
        ];
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Browser $browser, string $currentPassword, string $newPassword): void
    {
        $browser->type('@current_password', $currentPassword)
                ->type('@password', $newPassword)
                ->type('@password_confirmation', $newPassword)
                ->click('@save-button')
                ->waitForText('Saved');
    }
}
