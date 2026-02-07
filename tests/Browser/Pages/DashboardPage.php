<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class DashboardPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/dashboard';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Dashboard')
                ->waitFor('[data-slot="card"]')
                ->assertSee('Workflow Status')
                ->assertPresent('@workflow-status')
                ->assertPresent('@page-manager');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@workflow-status' => '[data-slot="card"]',
            '@page-manager' => 'button span:contains("Pages")',
            '@editor-tabs' => '[role="tablist"]',
            '@radial-chart' => '.recharts-responsive-container',
            '@traffic-lights' => '.rounded-full',
        ];
    }

    /**
     * Open the page manager sheet.
     */
    public function openPageManager(Browser $browser): void
    {
        $browser->click('@page-manager')
                ->waitFor('[data-slot="sheet-content"]')
                ->assertSee('Pages');
    }

    /**
     * Wait for the workflow status to update.
     */
    public function waitForWorkflowStatusUpdate(Browser $browser, string $expectedStatus): void
    {
        $browser->waitUntilMissing('.bg-yellow-500', 10)
                ->assertVisible('.bg-blue-500');
    }
}
