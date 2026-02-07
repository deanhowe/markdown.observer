<?php

namespace App\Repositories\Interfaces;

interface PageRepositoryInterface
{
    /**
     * Set the disk to use for storage operations
     *
     * @param string $disk
     * @return void
     */
    public function setDisk(string $disk): void;

    /**
     * Get all pages
     *
     * @return array
     */
    public function getAllPages(): array;

    /**
     * Get a page by filename
     *
     * @param string $filename
     * @return array|null
     */
    public function getPageByFilename(string $filename): ?array;

    /**
     * Create a new page
     *
     * @param string $filename
     * @param string $markdownContent
     * @param array|null $tiptapJson
     * @return array
     */
    public function createPage(string $filename, string $markdownContent, ?array $tiptapJson = null): array;

    /**
     * Update a page
     *
     * @param string $filename
     * @param string $markdownContent
     * @param array|null $tiptapJson
     * @return array
     */
    public function updatePage(string $filename, string $markdownContent, ?array $tiptapJson = null): array;

    /**
     * Delete a page
     *
     * @param string $filename
     * @return bool
     */
    public function deletePage(string $filename): bool;

    /**
     * Check if a page exists
     *
     * @param string $filename
     * @return bool
     */
    public function pageExists(string $filename): bool;

    /**
     * Sanitize a filename
     *
     * @param string $filename
     * @return string
     */
    public function sanitizeFilename(string $filename): string;
}
