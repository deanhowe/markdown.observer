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

test('carousel panels styling', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new HomePage)
            ->pause(4000); // Give the page more time to fully load, including the carousel

        // Take a screenshot first to see what's on the page
        $browser->screenshot('homepage-before-assertions');

        // Check for carousel navigation elements
        $browser->assertPresent('button[aria-label="Previous slide"]')
            ->assertPresent('button[aria-label="Next slide"]')
            ->assertPresent('[role="region"][aria-roledescription="carousel"]')
            ->assertPresent('[aria-roledescription="slide"]');

        // Check if the carousel cards have the correct classes for styling
        $browser->assertPresent('.aspect-\[335\/376\]')
            ->assertPresent('.lg\:aspect-auto')
            ->assertPresent('.lg\:h-\[438px\]')
            ->assertPresent('.overflow-hidden')
            ->assertPresent('.rounded-lg')
            ->assertPresent('.bg-\[\#fff2f2\]')
            ->assertPresent('.dark\:bg-\[\#1D0002\]');

        // Take a screenshot for visual inspection
        $browser->screenshot('carousel-styling-test');
    });
});
