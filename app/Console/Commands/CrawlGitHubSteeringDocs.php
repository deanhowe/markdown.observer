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
        
        // Get top starred repos
        $repos = $this->getTopRepos();
        
        foreach ($repos as $repo) {
            $this->crawlRepo($repo);
            sleep(1); // Rate limit friendly
        }
        
        $this->info("✅ Crawled " . ($this->crawled['found'] ?? 0) . " steering doc folders from " . ($this->crawled['checked'] ?? 0) . " repos");
    }

    private function getTopRepos(): array
    {
        // Top repos by stars (public data, no auth needed)
        return [
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
        ];
    }

    private function crawlRepo(string $repo): void
    {
        $this->crawled['checked'] = ($this->crawled['checked'] ?? 0) + 1;
        $this->line("Checking {$repo}...");

        foreach ($this->folders as $folder) {
            if ($this->checkFolder($repo, $folder)) {
                $this->info("  ✓ Found {$folder}/ in {$repo}");
                $this->crawled['found'] = ($this->crawled['found'] ?? 0) + 1;
                $this->downloadFolder($repo, $folder);
            }
        }
    }

    private function checkFolder(string $repo, string $folder): bool
    {
        $url = "https://api.github.com/repos/{$repo}/contents/{$folder}";
        $response = Http::withToken(config('services.github.token'))->get($url);
        return $response->successful();
    }

    private function downloadFolder(string $repo, string $folder): void
    {
        $url = "https://api.github.com/repos/{$repo}/contents/{$folder}";
        $response = Http::withToken(config('services.github.token'))->get($url);
        
        if (!$response->successful()) return;

        // Create collection (public, for search)
        $collection = SteeringCollection::firstOrCreate([
            'name' => "{$repo} ({$folder})",
            'type' => str_replace('.', '', $folder),
            'is_public' => true,
            'user_id' => 1, // System user
        ]);

        $files = $response->json();
        
        foreach ($files as $file) {
            if ($file['type'] === 'file') {
                $this->downloadFile($collection, $file);
            } elseif ($file['type'] === 'dir') {
                $this->downloadDirectory($collection, $file['path']);
            }
        }
    }

    private function downloadFile($collection, array $file): void
    {
        $content = Http::get($file['download_url'])->body();
        
        SteeringDoc::updateOrCreate(
            [
                'steering_collection_id' => $collection->id,
                'file_path' => $file['path'],
            ],
            [
                'content' => $content,
                'is_edited' => false,
            ]
        );
        
        $this->line("    → {$file['path']}");
    }

    private function downloadDirectory($collection, string $path): void
    {
        $repo = explode(' ', $collection->name)[0];
        $url = "https://api.github.com/repos/{$repo}/contents/{$path}";
        $response = Http::withToken(config('services.github.token'))->get($url);
        
        if (!$response->successful()) return;

        foreach ($response->json() as $file) {
            if ($file['type'] === 'file') {
                $this->downloadFile($collection, $file);
            }
        }
    }
}
