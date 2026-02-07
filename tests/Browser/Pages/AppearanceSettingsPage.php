<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class AppearanceSettingsPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/settings/appearance';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Appearance')
                ->assertPresent('@theme-selector');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@theme-selector' => '[data-slot="dropdown-menu"]',
            '@light-theme' => 'button:contains("Light")',
            '@dark-theme' => 'button:contains("Dark")',
            '@system-theme' => 'button:contains("System")',
        ];
    }

    /**
     * Select a theme.
     */
    public function selectTheme(Browser $browser, string $theme): void
    {
        $browser->click('@theme-selector');

        switch ($theme) {
            case 'light':
                $browser->click('@light-theme');
                break;
            case 'dark':
                $browser->click('@dark-theme');
                break;
            case 'system':
                $browser->click('@system-theme');
                break;
            default:
                throw new \InvalidArgumentException("Theme '{$theme}' is not supported.");
        }
    }
}
