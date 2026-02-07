<?php

namespace App\Actions;

use App\Services\PackageMarkdownService;

class RefreshPackageDataAction
{
    public function __construct(
        private readonly PackageMarkdownService $packageMarkdownService
    ) {}

    /**
     * Execute the action to refresh package data.
     *
     * @return bool
     */
    public function execute(): bool
    {
        return $this->packageMarkdownService->refreshPackageData();
    }
}
