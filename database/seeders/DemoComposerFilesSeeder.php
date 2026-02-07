<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DemoComposerFilesSeeder extends Seeder
{
    /**
     * Demo composer.json files from top PHP projects on GitHub
     * 
     * Files stored in database/seeders/demo-files/
     */
    public function run(): void
    {
        $demoDir = database_path('seeders/demo-files');
        $files = File::files($demoDir);
        
        dump("Found " . count($files) . " demo composer.json files");
        
        foreach ($files as $file) {
            $content = File::get($file);
            $filename = $file->getFilename();
            
            // Skip 404 responses
            if (str_contains($content, '404: Not Found')) {
                continue;
            }
            
            dump("✓ {$filename}");
        }
        
        dump("Demo files ready for testing upload flow");
    }
}
