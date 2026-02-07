<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SteeringCollection;
use App\Models\SteeringDoc;

class CrawlGitHubSteeringDocs extends Command
{
    protected $signature = 'crawl:steering-docs {--limit=100}';
    protected $description = 'Crawl GitHub for projects with AI steering docs';

    private array $folders = ['.claude', '.cursor', '.ai', '.kiro', '.aider', '.windsurf'];
    private array $crawled = [];

    public function handle()
    {
        $this->info('🚀 Starting GitHub steering docs crawler...');
        
        $repos = $this->getTopRepos();
        
        $this->info("Queueing {$repos->count()} repos...");
        
        foreach ($repos as $repo) {
            \App\Jobs\CrawlRepoSteeringDocs::dispatch($repo);
        }
        
        $this->info("✅ Queued all repos. Check Horizon for progress.");
    }

    private function getTopRepos()
    {
        return collect([
            'facebook/react',
            'vercel/next.js',
            'vuejs/core',
            'angular/angular',
            'sveltejs/svelte',
            'laravel/laravel',
            'symfony/symfony',
            'livewire/livewire',
            'filamentphp/filament',
            'tailwindlabs/tailwindcss',
            'prettier/prettier',
            'eslint/eslint',
            'vitejs/vite',
            'remix-run/remix',
            'nuxt/nuxt',
            'nestjs/nest',
            'strapi/strapi',
            'electron/electron',
        ]);
    }
}
