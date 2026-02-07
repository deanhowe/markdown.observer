<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PageRepositoryInterface;
use App\Services\MarkdownService;
use Illuminate\Support\Facades\Storage;

class PageRepository implements PageRepositoryInterface
{
    protected $disk = 'pages';
    private $markdownService;

    /**
     * Set the disk to use for storage operations
     *
     * @param string $disk
     * @return void
     */
    public function setDisk(string $disk): void
    {
        $this->disk = $disk;
    }

    public function __construct(MarkdownService $markdownService)
    {
        $this->markdownService = $markdownService;
    }

    /**
     * Get all pages
     *
     * @return array
     */
    public function getAllPages(): array
    {
        $files = Storage::disk($this->disk)->files();
        $pages = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $markdownContent = Storage::disk($this->disk)->get($file);
                $htmlContent = $this->markdownService->toHtml($markdownContent);

                // Get the latest revision to get tiptap_json if available
                $latestRevision = app(\App\Repositories\Interfaces\PageRevisionRepositoryInterface::class)
                    ->getLatestRevision($filename);

                $pages[] = [
                    'filename' => $filename,
                    'markdown_content' => $markdownContent,
                    'html_content' => $htmlContent,
                    'tiptap_json' => $latestRevision ? $latestRevision->tiptap_json : null,
                    'last_modified' => Storage::disk($this->disk)->lastModified($file),
                ];
            }
        }

        return $pages;
    }

    /**
     * Get a page by filename
     *
     * @param string $filename
     * @return array|null
     */
    public function getPageByFilename(string $filename): ?array
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        $filePath = $sanitizedFilename . '.md';

        if (!Storage::disk($this->disk)->exists($filePath)) {
            return null;
        }

        $markdownContent = Storage::disk($this->disk)->get($filePath);
        $htmlContent = $this->markdownService->toHtml($markdownContent);
        $fileLastModified = Storage::disk($this->disk)->lastModified($filePath);

        // Check if the file has been modified outside the application
        $hasExternalChanges = false;
        $latestRevision = app(\App\Repositories\Interfaces\PageRevisionRepositoryInterface::class)
            ->getLatestRevision($sanitizedFilename);

        if ($latestRevision) {
            // If we have a revision, check if the file has been modified since the last revision
            $hasExternalChanges = $fileLastModified > $latestRevision->created_at->timestamp;
        }

        return [
            'filename' => $sanitizedFilename,
            'markdown_content' => $markdownContent,
            'html_content' => $htmlContent,
            'tiptap_json' => $latestRevision ? $latestRevision->tiptap_json : null,
            'last_modified' => $fileLastModified,
            'has_external_changes' => $hasExternalChanges,
        ];
    }

    /**
     * Create a new page
     *
     * @param string $filename
     * @param string $markdownContent
     * @param array|null $tiptapJson
     * @return array
     */
    public function createPage(string $filename, string $markdownContent, ?array $tiptapJson = null): array
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        $filePath = $sanitizedFilename . '.md';

        if (Storage::disk($this->disk)->exists($filePath)) {
            throw new \Exception('File already exists');
        }

        $htmlContent = $this->markdownService->toHtml($markdownContent);

        // For new pages, always create the file as it's the initial source of truth
        Storage::disk($this->disk)->put($filePath, $markdownContent);

        // Get the last modified timestamp
        $fileLastModified = Storage::disk($this->disk)->lastModified($filePath);

        return [
            'filename' => $sanitizedFilename,
            'markdown_content' => $markdownContent,
            'html_content' => $htmlContent,
            'tiptap_json' => $tiptapJson,
            'last_modified' => $fileLastModified,
        ];
    }

    /**
     * Update a page
     *
     * @param string $filename
     * @param string $markdownContent
     * @param array|null $tiptapJson
     * @return array
     */
    public function updatePage(string $filename, string $markdownContent, ?array $tiptapJson = null): array
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        $filePath = $sanitizedFilename . '.md';

        if (!Storage::disk($this->disk)->exists($filePath)) {
            throw new \Exception('Page not found');
        }

        $htmlContent = $this->markdownService->toHtml($markdownContent);

        // Always update the file when the page is updated
        Storage::disk($this->disk)->put($filePath, $markdownContent);

        // Get the last modified timestamp
        $fileLastModified = Storage::disk($this->disk)->lastModified($filePath);

        return [
            'filename' => $sanitizedFilename,
            'markdown_content' => $markdownContent,
            'html_content' => $htmlContent,
            'tiptap_json' => $tiptapJson,
            'last_modified' => $fileLastModified,
        ];
    }

    /**
     * Delete a page
     *
     * @param string $filename
     * @return bool
     */
    public function deletePage(string $filename): bool
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        $filePath = $sanitizedFilename . '.md';

        if (!Storage::disk($this->disk)->exists($filePath)) {
            return false;
        }

        // Delete the file
        Storage::disk($this->disk)->delete($filePath);

        return true;
    }

    /**
     * Check if a page exists
     *
     * @param string $filename
     * @return bool
     */
    public function pageExists(string $filename): bool
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        $filePath = $sanitizedFilename . '.md';

        return Storage::disk($this->disk)->exists($filePath);
    }

    /**
     * Sanitize a filename
     *
     * @param string $filename
     * @return string
     */
    public function sanitizeFilename(string $filename): string
    {
        // Remove any path information
        $filename = basename($filename);
        // Replace any character that is not a letter, number, dash, or underscore with a hyphen
        $filename = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $filename);
        return $filename;
    }
}
