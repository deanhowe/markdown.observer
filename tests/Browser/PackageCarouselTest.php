<?php

use Laravel\Dusk\Browser;

test('package carousel is displayed on homepage', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(3000) // Give the page more time to load
            ->assertPresent('button[aria-label="Previous slide"]')
            ->assertPresent('button[aria-label="Next slide"]');

        // Take a screenshot for debugging
        $browser->screenshot('package-carousel-test');
    });
});

test('carousel navigation works', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(3000) // Give the page more time to load
            ->assertPresent('button[aria-label="Next slide"]')
            ->click('button[aria-label="Next slide"]')
            ->pause(1000) // Wait for animation
            ->assertPresent('button[aria-label="Previous slide"]')
            ->click('button[aria-label="Previous slide"]')
            ->pause(1000); // Wait for animation

        // Take a screenshot for debugging
        $browser->screenshot('carousel-navigation-test');
    });
});

/**
 * Setup before each test.
 */
beforeEach(function () {
    // Make sure the composer-details.json file exists
    if (! file_exists(database_path('data/composer-details.json'))) {
        $this->artisan('app:analyze-composer-packages');
    }
});
