<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserJourneyTest extends DuskTestCase
{
    public function test_complete_user_journey()
    {
        $this->browse(function (Browser $browser) {
            // 1. Visit homepage
            $browser->visit('/')
                    ->assertSee('Your Package Documentation')
                    ->screenshot('01-homepage');

            // 2. Register
            $browser->clickLink('Get Started')
                    ->assertPathIs('/register')
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('Register')
                    ->screenshot('02-register');

            // 3. Should be on dashboard
            $browser->waitForLocation('/dashboard')
                    ->assertSee('Your Packages')
                    ->screenshot('03-dashboard-empty');

            // 4. Upload packages
            $browser->clickLink('Upload Packages')
                    ->assertPathIs('/upload')
                    ->screenshot('04-upload-page');

            // Create test composer.json
            $composerJson = json_encode([
                'require' => [
                    'laravel/framework' => '^12.0',
                    'spatie/laravel-data' => '^4.0',
                ]
            ]);
            file_put_contents('/tmp/test-composer.json', $composerJson);

            $browser->attach('composer_json', '/tmp/test-composer.json')
                    ->press('Upload & Analyze')
                    ->screenshot('05-package-selection');

            // 5. Select packages
            $browser->waitForText('Select Packages')
                    ->check('input[type="checkbox"]')
                    ->press('Add')
                    ->screenshot('06-packages-selected');

            // 6. Back to dashboard with packages
            $browser->waitForLocation('/dashboard')
                    ->assertSee('laravel/framework')
                    ->screenshot('07-dashboard-with-packages');

            // 7. View package docs
            $browser->clickLink('laravel/framework')
                    ->waitForText('README.md')
                    ->screenshot('08-package-docs');

            // 8. Edit a doc
            $browser->press('Edit')
                    ->type('textarea', '# Test Edit')
                    ->press('Save')
                    ->screenshot('09-doc-edited');

            // Cleanup
            unlink('/tmp/test-composer.json');
        });
    }

    public function test_pricing_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pricing')
                    ->assertSee('Simple Pricing')
                    ->assertSee('Free')
                    ->assertSee('Pro')
                    ->assertSee('Lifetime')
                    ->screenshot('10-pricing');
        });
    }
}
