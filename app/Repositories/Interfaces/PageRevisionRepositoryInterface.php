<?php

namespace App\Repositories\Interfaces;

use App\Models\PageRevision;

interface PageRevisionRepositoryInterface
{
    /**
     * Get the latest revision for a file
     *
     * @param string $filename
     * @return PageRevision|null
     */
    public function getLatestRevision(string $filename): ?PageRevision;

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
    ): PageRevision;

    /**
     * Get all revisions for a file
     *
     * @param string $filename
     * @return array
     */
    public function getRevisionHistory(string $filename): array;

    /**
     * Check if a file has any revisions
     *
     * @param string $filename
     * @return bool
     */
    public function hasRevisions(string $filename): bool;
}
