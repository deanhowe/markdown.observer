<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class HomePage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Laravel');

        // Check for Composer Packages section if it's loaded
        $browser->pause(1000); // Give time for the carousel to load
        if ($browser->element('@package-carousel')) {
            $browser->assertSee('Composer Packages');
        }
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@logo' => 'svg.h-16',
            '@documentation' => 'a[href*="documentation"]',
            '@news' => 'a[href*="news"]',
            '@github' => 'a[href*="github"]',
            '@package-carousel' => 'div:has(h2:contains("Composer Packages"))',
            '@carousel-content' => '[data-slot="carousel-content"]',
            '@carousel-item' => '[data-slot="carousel-item"]',
            '@carousel-previous' => '[data-slot="carousel-previous"]',
            '@carousel-next' => '[data-slot="carousel-next"]',
        ];
    }
}
