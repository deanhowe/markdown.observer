<?php

namespace App\Services;

use App\Repositories\Interfaces\PageRepositoryInterface;
use App\Repositories\Interfaces\PageRevisionRepositoryInterface;
use Illuminate\Support\Facades\Log;

class PageService
{
    protected $pageRepository;
    protected $pageRevisionRepository;
    protected $markdownService;
    protected $disk = 'pages';

    public function __construct(
        PageRepositoryInterface $pageRepository,
        PageRevisionRepositoryInterface $pageRevisionRepository,
        MarkdownService $markdownService,
        string $disk = null
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageRevisionRepository = $pageRevisionRepository;
        $this->markdownService = $markdownService;

        // Set the default disk from config or parameter
        $this->setDisk($disk ?? config('content.default_disk', 'pages'));
    }

    /**
     * Set the disk to use for storage operations
     *
     * @param string $disk
     * @return void
     */
    public function setDisk(string $disk): void
    {
        $this->disk = $disk;
        $this->pageRepository->setDisk($disk);
    }

    /**
     * Get all pages
     *
     * @return array
     */
    public function getAllPages(): array
    {
        return $this->pageRepository->getAllPages();
    }

    /**
     * Get a page by filename
     *
     * @param string $filename
     * @return array|null
     */
    public function getPageByFilename(string $filename): ?array
    {
        $page = $this->pageRepository->getPageByFilename($filename);

        if (!$page) {
            return null;
        }

        // Check if we have a revision in the database
        $revision = $this->pageRevisionRepository->getLatestRevision($filename);
        if ($revision) {
            $page['tiptap_json'] = $revision->tiptap_json;
        }

        return $page;
    }

    /**
     * Create a new page
     *
     * @param string $filename
     * @param string $markdownContent
     * @param array|null $tiptapJson
     * @return array
     * @throws \Exception
     */
    public function createPage(string $filename, string $markdownContent, ?array $tiptapJson = null): array
    {
        try {
            // Create the page
            $page = $this->pageRepository->createPage($filename, $markdownContent, $tiptapJson);

            // Track the change in the database
            $htmlContent = $page['html_content'];
            $this->pageRevisionRepository->createRevision(
                $page['filename'],
                $markdownContent,
                $htmlContent,
                $tiptapJson,
                'create'
            );

            return $page;
        } catch (\Exception $e) {
            Log::error('Failed to create page: ' . $e->getMessage(), [
                'filename' => $filename,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Update a page
     *
     * @param string $filename
     * @param string $markdownContent
     * @param array|null $tiptapJson
     * @param bool $forceUpdate
     * @return array
     * @throws \Exception
     */
    public function updatePage(string $filename, string $markdownContent, ?array $tiptapJson = null, bool $forceUpdate = false): array
    {
        try {
            // Get the current page state
            $currentPage = $this->pageRepository->getPageByFilename($filename);

            if (!$currentPage) {
                throw new \Exception('Page not found');
            }

            // Check for external changes
            if (!$forceUpdate && isset($currentPage['has_external_changes']) && $currentPage['has_external_changes']) {
                // Create a conflict revision to preserve the external changes
                $this->pageRevisionRepository->createRevision(
                    $currentPage['filename'],
                    $currentPage['markdown_content'],
                    $currentPage['html_content'],
                    null,
                    'conflict'
                );

                // Throw an exception to notify the user about the conflict
                throw new \Exception('The file has been modified outside the application. Please resolve the conflict or use forceUpdate=true to overwrite the changes.');
            }

            // Update the page
            $page = $this->pageRepository->updatePage($filename, $markdownContent, $tiptapJson);

            // Track the change in the database
            $htmlContent = $page['html_content'];
            $this->pageRevisionRepository->createRevision(
                $page['filename'],
                $markdownContent,
                $htmlContent,
                $tiptapJson,
                'update'
            );

            return $page;
        } catch (\Exception $e) {
            Log::error('Failed to update page: ' . $e->getMessage(), [
                'filename' => $filename,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Delete a page
     *
     * @param string $filename
     * @return bool
     * @throws \Exception
     */
    public function deletePage(string $filename): bool
    {
        try {
            // Get the page content before deleting
            $page = $this->pageRepository->getPageByFilename($filename);

            if (!$page) {
                throw new \Exception('Page not found');
            }

            // Delete the page
            $deleted = $this->pageRepository->deletePage($filename);

            if (!$deleted) {
                throw new \Exception('Failed to delete page');
            }

            // Track the deletion in the database
            $this->pageRevisionRepository->createRevision(
                $page['filename'],
                $page['markdown_content'],
                $page['html_content'],
                null,
                'delete'
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete page: ' . $e->getMessage(), [
                'filename' => $filename,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Convert Markdown to HTML
     *
     * @param string $markdown
     * @return string
     */
    public function convertToHtml(string $markdown): string
    {
        return $this->markdownService->toHtml($markdown);
    }

    /**
     * Convert HTML to Markdown
     *
     * @param string $html
     * @return string
     */
    public function convertToMarkdown(string $html): string
    {
        return $this->markdownService->toMarkdown($html);
    }
}
