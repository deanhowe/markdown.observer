<?php

namespace App\Console\Commands;

use App\Events\MarkdownChanged;
use App\Models\PageRevision;
use App\Services\MarkdownService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DetectExternalMarkdownChanges extends Command
{
    protected $signature = 'markdown:detect-external';
    protected $description = 'Detect Markdown changes made outside Markdown.Observer and publish them';

    public function __construct(private readonly MarkdownService $markdownService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $diskName = config('content.default_disk', 'pages');
        $disk = Storage::disk($diskName);
        $detected = 0;

        foreach ($disk->files() as $path) {
            if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'md') {
                continue;
            }

            $content = $disk->get($path);
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $hash = hash('sha256', $content);
            $latest = PageRevision::where('filename', $filename)->latest()->first();
            if ($latest && hash('sha256', $latest->markdown_content) === $hash) {
                continue;
            }

            $revision = PageRevision::createRevision(
                $filename,
                $content,
                $this->markdownService->toHtml($content),
                null,
                'external'
            );
            MarkdownChanged::dispatch(
                source: 'filesystem',
                repository: (string) config('services.moof_forge.repository', 'markdown.observer'),
                path: $path,
                content: $content,
                contentHash: $hash,
                changedAt: now()->toIso8601String(),
                metadata: ['revision_id' => $revision->id, 'revision_type' => 'external'],
            );
            $detected++;
        }

        $this->info("Detected {$detected} external Markdown change(s).");
        return self::SUCCESS;
    }
}
