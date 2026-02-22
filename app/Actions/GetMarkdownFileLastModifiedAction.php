<?php

namespace App\Actions;

use App\Services\PackageMarkdownService;
use Illuminate\Support\Facades\Storage;

class GetMarkdownFileLastModifiedAction
{
    public function __construct(
        private readonly PackageMarkdownService $packageMarkdownService
    ) {}

    /**
     * Get the last modified timestamp of a markdown file.
     *
     * @param string $packageName
     * @param string $filePath
     * @return int|null
     */
    public function execute(string $packageName, string $filePath): ?int
    {
        $file = $this->packageMarkdownService->getPackageMarkdownFile($packageName, $filePath);

        if (!$file) {
            return null;
        }

        // Try storage_path first
        if (isset($file['storage_path'])) {
            if (Storage::disk('public')->exists($file['storage_path'])) {
                return Storage::disk('public')->lastModified($file['storage_path']);
            }
        }

        // Try relative_path
        if (isset($file['relative_path'])) {
            $relativePath = $file['relative_path'];
            if (Storage::disk('local')->exists($relativePath)) {
                return Storage::disk('local')->lastModified($relativePath);
            }
        }

        // If it's just raw content in JSON, we can't really "watch" it for changes on disk
        // but we can return the current time or a fixed value.
        // For polling, we'll return null if it's not a real file.
        return null;
    }
}
