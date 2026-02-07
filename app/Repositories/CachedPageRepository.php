<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CachedPageRepository implements PageRepositoryInterface
{
    private $repository;
    private $cachePrefix = 'page_';
    private $cacheTtl = 86400; // 24 hours in seconds
    private $disk = 'pages'; // Default disk

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
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
        $this->repository->setDisk($disk);

        // Invalidate all caches for this repository when changing disks
        $this->invalidateAllPagesCache();
    }

    /**
     * Get all pages
     *
     * @return array
     */
    public function getAllPages(): array
    {
        return Cache::remember($this->cachePrefix . $this->disk . '_all', $this->cacheTtl, function () {
            return $this->repository->getAllPages();
        });
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
        return Cache::remember($this->cachePrefix . $this->disk . '_' . $sanitizedFilename, $this->cacheTtl, function () use ($filename) {
            return $this->repository->getPageByFilename($filename);
        });
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
        $result = $this->repository->createPage($filename, $markdownContent, $tiptapJson);
        $this->invalidateCache($filename);
        $this->invalidateAllPagesCache();
        return $result;
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
        $result = $this->repository->updatePage($filename, $markdownContent, $tiptapJson);
        $this->invalidateCache($filename);
        return $result;
    }

    /**
     * Delete a page
     *
     * @param string $filename
     * @return bool
     */
    public function deletePage(string $filename): bool
    {
        $result = $this->repository->deletePage($filename);
        if ($result) {
            $this->invalidateCache($filename);
            $this->invalidateAllPagesCache();
        }
        return $result;
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
        return Cache::remember($this->cachePrefix . $this->disk . '_exists_' . $sanitizedFilename, $this->cacheTtl, function () use ($filename) {
            return $this->repository->pageExists($filename);
        });
    }

    /**
     * Sanitize a filename
     *
     * @param string $filename
     * @return string
     */
    public function sanitizeFilename(string $filename): string
    {
        return $this->repository->sanitizeFilename($filename);
    }

    /**
     * Invalidate cache for a specific page
     *
     * @param string $filename
     * @return void
     */
    private function invalidateCache(string $filename): void
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        Cache::forget($this->cachePrefix . $this->disk . '_' . $sanitizedFilename);
        Cache::forget($this->cachePrefix . $this->disk . '_exists_' . $sanitizedFilename);
    }

    /**
     * Invalidate cache for all pages
     *
     * @return void
     */
    private function invalidateAllPagesCache(): void
    {
        Cache::forget($this->cachePrefix . $this->disk . '_all');
    }
}
