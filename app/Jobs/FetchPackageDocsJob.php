<?php

namespace App\Jobs;

use App\Models\UserPackage;
use App\Models\PackageDoc;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchPackageDocsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserPackage $package
    ) {}

    public function handle(): void
    {
        $repo = $this->package->package_name;
        $branch = 'master';
        
        // Try to fetch docs directory
        $response = Http::get("https://api.github.com/repos/{$repo}/contents/docs?ref={$branch}");
        
        if ($response->failed()) {
            $response = Http::get("https://api.github.com/repos/{$repo}/contents/docs?ref=main");
            $branch = 'main';
        }
        
        if ($response->successful()) {
            $files = $response->json();
            
            foreach ($files as $file) {
                if ($file['type'] === 'file' && str_ends_with($file['name'], '.md')) {
                    $content = Http::get($file['download_url'])->body();
                    
                    PackageDoc::updateOrCreate(
                        [
                            'user_id' => $this->package->user_id,
                            'package_name' => $this->package->package_name,
                            'file_path' => 'docs/' . $file['name'],
                        ],
                        [
                            'content' => $content,
                            'original_content' => $content,
                            'upstream_hash' => md5($content),
                            'is_edited' => false,
                        ]
                    );
                }
            }
        }
        
        // Fetch README
        $readme = Http::get("https://raw.githubusercontent.com/{$repo}/{$branch}/README.md");
        if ($readme->successful()) {
            PackageDoc::updateOrCreate(
                [
                    'user_id' => $this->package->user_id,
                    'package_name' => $this->package->package_name,
                    'file_path' => 'README.md',
                ],
                [
                    'content' => $readme->body(),
                    'original_content' => $readme->body(),
                    'upstream_hash' => md5($readme->body()),
                    'is_edited' => false,
                ]
            );
        }
        
        $this->package->update(['last_synced_at' => now()]);
    }
}
