<?php

namespace App\Repositories;

use App\Models\PageRevision;
use App\Repositories\Interfaces\PageRevisionRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CachedPageRevisionRepository implements PageRevisionRepositoryInterface
{
    private $repository;
    private $cachePrefix = 'page_revision_';
    private $cacheTtl = 86400; // 24 hours in seconds

    public function __construct(PageRevisionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get the latest revision for a file
     *
     * @param string $filename
     * @return PageRevision|null
     */
    public function getLatestRevision(string $filename): ?PageRevision
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        return Cache::remember($this->cachePrefix . 'latest_' . $sanitizedFilename, $this->cacheTtl, function () use ($filename) {
            return $this->repository->getLatestRevision($filename);
        });
    }

    /**
     * Create a new revision for a file
     *
     * @param string $filename
     * @param string $markdownContent
     * @param string $htmlContent
     * @param array|null $tiptapJson
     * @param string $revisionType
     * @return PageRevision
     */
    public function createRevision(
        string $filename,
        string $markdownContent,
        string $htmlContent,
        ?array $tiptapJson = null,
        string $revisionType = 'update'
    ): PageRevision {
        $result = $this->repository->createRevision(
            $filename,
            $markdownContent,
            $htmlContent,
            $tiptapJson,
            $revisionType
        );

        $this->invalidateCache($filename);
        return $result;
    }

    /**
     * Get all revisions for a file
     *
     * @param string $filename
     * @return array
     */
    public function getRevisionHistory(string $filename): array
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        return Cache::remember($this->cachePrefix . 'history_' . $sanitizedFilename, $this->cacheTtl, function () use ($filename) {
            return $this->repository->getRevisionHistory($filename);
        });
    }

    /**
     * Sanitize a filename for use in cache keys
     *
     * @param string $filename
     * @return string
     */
    private function sanitizeFilename(string $filename): string
    {
        return str_replace(['/', '.'], ['_', '_'], $filename);
    }

    /**
     * Invalidate cache for a specific file
     *
     * @param string $filename
     * @return void
     */
    private function invalidateCache(string $filename): void
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        Cache::forget($this->cachePrefix . 'latest_' . $sanitizedFilename);
        Cache::forget($this->cachePrefix . 'history_' . $sanitizedFilename);
        Cache::forget($this->cachePrefix . 'has_revisions_' . $sanitizedFilename);
    }

    /**
     * Check if a file has any revisions
     *
     * @param string $filename
     * @return bool
     */
    public function hasRevisions(string $filename): bool
    {
        $sanitizedFilename = $this->sanitizeFilename($filename);
        return Cache::remember($this->cachePrefix . 'has_revisions_' . $sanitizedFilename, $this->cacheTtl, function () use ($filename) {
            return $this->repository->hasRevisions($filename);
        });
    }
}
