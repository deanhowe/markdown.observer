<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page as BasePage;

abstract class Page extends BasePage
{
    /**
     * Get the global element shortcuts for the site.
     *
     * @return array<string, string>
     */
    public static function siteElements(): array
    {
        return [
            '@sidebar' => '[data-slot="sidebar"]',
            '@header' => '[data-slot="header"]',
            '@footer' => '[data-slot="footer"]',
            '@logo' => 'svg.h-6',
            '@user-dropdown' => '[data-slot="dropdown-menu"]',
            '@dashboard-link' => 'a[href*="dashboard"]',
            '@pages-link' => 'button span:contains("Pages")',
            '@settings-link' => 'a[href*="settings"]',
            '@logout-button' => 'button:contains("Log Out")',
            '@toast' => '[data-slot="toast"]',
        ];
    }
}
