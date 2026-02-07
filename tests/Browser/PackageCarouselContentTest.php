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

test('package information is displayed', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(4000) // Give the page more time to load
            ->assertPresent('.text-lg.font-semibold') // Package name
            ->assertPresent('p.text-sm') // Package description
            ->assertSeeIn('p.text-sm.text-gray-500', 'Version:'); // Package version

        // Take a screenshot for debugging
        $browser->screenshot('package-information-test');
    });
});

test('package logos are displayed', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(4000) // Give the page more time to load
            ->assertPresent('.mb-4.h-24.flex.items-center.justify-center') // Logo container
            ->assertPresent('img.max-h-20.max-w-full.object-contain'); // Logo image

        // Take a screenshot for debugging
        $browser->screenshot('package-logos-test');
    });
});

test('readme html is rendered', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(4000) // Give the page more time to load
            ->assertPresent('.readme-content') // README container
            ->assertPresent('.readme-content h1, .readme-content h2, .readme-content p'); // Common README elements

        // Take a screenshot for debugging
        $browser->screenshot('readme-html-test');
    });
});

test('package counter is displayed', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(4000) // Give the page more time to load
            ->assertPresent('.text-center.mt-4') // Counter container
            ->assertPresent('.text-sm.font-medium'); // Counter text

        // Take a screenshot for debugging
        $browser->screenshot('package-counter-test');
    });
});

test('indicator dots are displayed and functional', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(4000) // Give the page more time to load
            ->assertPresent('.flex.justify-center.gap-1.mt-2') // Dots container
            ->assertPresent('.h-2.w-2.rounded-full'); // Dot

        // Click on the second dot (if it exists)
        if ($browser->elements('.h-2.w-2.rounded-full')[1]) {
            $browser->click('.h-2.w-2.rounded-full:nth-child(2)')
                ->pause(1000); // Wait for animation
        }

        // Take a screenshot for debugging
        $browser->screenshot('indicator-dots-test');
    });
});

test('error state handling', function () {
    $this->browse(function (Browser $browser) {
        // Visit a non-existent API endpoint to trigger an error
        $browser->visit('/api/packages/non-existent')
            ->assertSee('404')
            ->screenshot('api-error-test');

        // Check that the main page still loads even if the API fails
        $browser->visit('/')
            ->pause(4000)
            ->assertPresent('body') // The page should still load
            ->screenshot('main-page-after-api-error');
    });
});

test('empty state handling', function () {
    // This test is a placeholder and may need to be adapted based on how empty states are handled in your application
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->pause(4000);

        // If there are no packages, we should see a message
        if (! $browser->element('.carousel-item')) {
            $browser->assertSee('No Packages Found')
                ->screenshot('empty-state-test');
        } else {
            $this->markTestSkipped('Cannot test empty state because packages are present.');
        }
    });
});
