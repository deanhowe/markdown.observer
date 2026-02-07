<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GenerateOgImagesTest extends DuskTestCase
{
    public function test_generate_og_images()
    {
        $pages = [
            'home' => '/',
            'pricing' => '/pricing',
            'ai' => '/ai',
        ];

        foreach ($pages as $name => $path) {
            $this->browse(function (Browser $browser) use ($name, $path) {
                $browser->visit($path)
                    ->pause(3000) // Wait for page load + animations
                    ->resize(1200, 630)
                    ->screenshot("og-{$name}");
                    
                echo "✓ Generated og-{$name}.png\n";
            });
        }
    }
}
