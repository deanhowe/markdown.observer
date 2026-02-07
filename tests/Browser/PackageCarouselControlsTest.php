<?php

use Laravel\Dusk\Browser;

/**
 * Ensure dataset exists for the homepage carousel.
 */
beforeEach(function () {
    if (! file_exists(database_path('data/composer-details.json'))) {
        $this->artisan('app:analyze-composer-packages');
    }
});

test('carousel controls render on homepage', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(1500)
            ->assertPresent('[data-testid="carousel-limit"]')
            ->assertPresent('[data-testid="carousel-order-by"]')
            ->assertPresent('[data-testid="carousel-direction"]')
            ->assertPresent('[data-testid="carousel-type"]')
            ->assertPresent('[data-testid="carousel-include-readme"]');
    });
});
