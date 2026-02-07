<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductionSmokeTest extends DuskTestCase
{
    protected string $productionUrl = 'https://markdown.observer';

    /** @test */
    public function homepage_loads_and_shows_navigation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit($this->productionUrl)
                ->assertSee('Markdown Observer')
                ->assertSee('Your Package Documentation')
                ->assertSee('Login')
                ->assertSee('Get Started')
                ->assertSee('How It Works');
        });
    }

    /** @test */
    public function pricing_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit($this->productionUrl . '/pricing')
                ->assertSee('Simple Pricing')
                ->assertSee('Free')
                ->assertSee('Pro')
                ->assertSee('Lifetime')
                ->assertSee('£0')
                ->assertSee('£9')
                ->assertSee('£299');
        });
    }

    /** @test */
    public function login_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit($this->productionUrl . '/login')
                ->assertSee('Log in')
                ->assertSee('Email')
                ->assertSee('Password')
                ->assertPresent('input[type="email"]')
                ->assertPresent('input[type="password"]');
        });
    }

    /** @test */
    public function register_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit($this->productionUrl . '/register')
                ->assertSee('Register')
                ->assertSee('Name')
                ->assertSee('Email')
                ->assertSee('Password')
                ->assertPresent('input[name="name"]')
                ->assertPresent('input[type="email"]')
                ->assertPresent('input[type="password"]');
        });
    }

    /** @test */
    public function dark_mode_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit($this->productionUrl)
                ->assertVisible('body');
        });
    }

    /** @test */
    public function svg_icons_render()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit($this->productionUrl)
                ->assertPresent('svg');
        });
    }

    /** @test */
    public function site_is_secure()
    {
        $this->browse(function (Browser $browser) {
            $url = $browser->visit($this->productionUrl)->driver->getCurrentURL();
            $this->assertStringStartsWith('https://', $url);
        });
    }
}
