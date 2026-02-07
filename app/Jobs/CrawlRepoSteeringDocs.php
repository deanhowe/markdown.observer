<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\SteeringCollection;
use App\Models\SteeringDoc;

class CrawlRepoSteeringDocs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $repo,
        public array $folders = ['.claude', '.cursor', '.ai', '.kiro', '.aider', '.windsurf']
    ) {}

    public function handle(): void
    {
        foreach ($this->folders as $folder) {
            if ($this->checkFolder($folder)) {
                $this->downloadFolder($folder);
            }
        }
    }

    private function checkFolder(string $folder): bool
    {
        $url = "https://api.github.com/repos/{$this->repo}/contents/{$folder}";
        $response = Http::withToken(config('services.github.token'))->get($url);
        return $response->successful();
    }

    private function downloadFolder(string $folder): void
    {
        $url = "https://api.github.com/repos/{$this->repo}/contents/{$folder}";
        $response = Http::withToken(config('services.github.token'))->get($url);
        
        if (!$response->successful()) return;

        $collection = SteeringCollection::firstOrCreate([
            'name' => "{$this->repo} ({$folder})",
            'type' => str_replace('.', '', $folder),
            'is_public' => true,
            'user_id' => 1,
        ]);

        foreach ($response->json() as $file) {
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
    }

    private function downloadDirectory($collection, string $path): void
    {
        $url = "https://api.github.com/repos/{$this->repo}/contents/{$path}";
        $response = Http::withToken(config('services.github.token'))->get($url);
        
        if (!$response->successful()) return;

        foreach ($response->json() as $file) {
            if ($file['type'] === 'file') {
                $this->downloadFile($collection, $file);
            }
        }
    }
}
