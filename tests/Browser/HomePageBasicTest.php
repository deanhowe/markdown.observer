<?php

use Laravel\Dusk\Browser;

/**
 * Setup before each test.
 */
beforeEach(function () {
    // Make sure the composer-details.json file exists
    if (! file_exists(database_path('data/composer-details.json'))) {
        $this->artisan('app:analyze-composer-packages');
    }
});

test('homepage loads', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('Laravel');
    });
});

test('composer packages carousel is displayed', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(4000); // Give the page more time to load

        // Check for error messages
        $hasError = $browser->element('.text-red-500') !== null;
        if ($hasError) {
            echo 'Error message found: '.$browser->text('.text-red-500')."\n";
        }

        // Check for loading message
        $isLoading = $browser->element('div:contains("Loading packages...")') !== null;
        if ($isLoading) {
            echo 'Loading message found: '.$browser->text('div:contains("Loading packages...")')."\n";
        }

        // Take a screenshot for debugging
        $browser->screenshot('composer-packages-carousel');

        // Output the page source for debugging
        echo 'Page source: '.$browser->driver->getPageSource()."\n";
    });
});
