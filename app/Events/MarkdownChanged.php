<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * One normalized Markdown change, regardless of whether the bytes came from
 * the local editor, the filesystem, or an external Git crawler.
 */
class MarkdownChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $source,
        public string $repository,
        public string $path,
        public string $content,
        public string $contentHash,
        public string $changedAt,
        public ?string $commit = null,
        public array $metadata = [],
    ) {}
}
