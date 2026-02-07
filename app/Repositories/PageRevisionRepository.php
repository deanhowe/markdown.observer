<?php

namespace App\Repositories;

use App\Models\PageRevision;
use App\Repositories\Interfaces\PageRevisionRepositoryInterface;

class PageRevisionRepository implements PageRevisionRepositoryInterface
{
    /**
     * Get the latest revision for a file
     *
     * @param string $filename
     * @return PageRevision|null
     */
    public function getLatestRevision(string $filename): ?PageRevision
    {
        return PageRevision::where('filename', $filename)
            ->latest()
            ->first();
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
        return PageRevision::create([
            'filename' => $filename,
            'markdown_content' => $markdownContent,
            'html_content' => $htmlContent,
            'tiptap_json' => $tiptapJson,
            'revision_type' => $revisionType,
        ]);
    }

    /**
     * Get all revisions for a file
     *
     * @param string $filename
     * @return array
     */
    public function getRevisionHistory(string $filename): array
    {
        return PageRevision::where('filename', $filename)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Check if a file has any revisions
     *
     * @param string $filename
     * @return bool
     */
    public function hasRevisions(string $filename): bool
    {
        return PageRevision::where('filename', $filename)->exists();
    }
}
