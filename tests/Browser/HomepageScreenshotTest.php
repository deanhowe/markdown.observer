<?php

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomePage;

/**
 * Setup before each test.
 */
beforeEach(function () {
    // Make sure the composer-details.json file exists
    if (! file_exists(database_path('data/composer-details.json'))) {
        $this->artisan('app:analyze-composer-packages');
    }
});

test('homepage screenshot', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new HomePage)
            ->pause(4000) // Give the page more time to fully load, including the carousel
            ->screenshot('homepage-full-screenshot');

        // The screenshot will be saved to tests/Browser/screenshots/homepage-full-screenshot.png
    });
});
