<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ProfileSettingsPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/settings/profile';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Profile')
                ->assertPresent('@name')
                ->assertPresent('@email')
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
            '@name' => 'input[name="name"]',
            '@email' => 'input[type="email"]',
            '@save-button' => 'button[type="submit"]',
            '@delete-account' => 'button:contains("Delete Account")',
        ];
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Browser $browser, string $name, string $email): void
    {
        $browser->type('@name', $name)
                ->type('@email', $email)
                ->click('@save-button')
                ->waitForText('Saved');
    }
}
