<?php

namespace App\Actions;

use App\DataTransferObjects\PackageIndexData;
use App\Services\PackageMarkdownService;
use Spatie\LaravelData\Data;

class GetPackagesIndexAction
{
    public function __construct(
        private readonly PackageMarkdownService $packageMarkdownService
    ) {}

    /**
     * Execute the action to get packages for the index page.
     *
     * @return PackageIndexData
     */
    public function execute(): PackageIndexData
    {
        $packages = $this->packageMarkdownService->getPackagesByRank();
        $prodPackages = $this->packageMarkdownService->getPackagesByType('prod');
        $devPackages = $this->packageMarkdownService->getPackagesByType('dev');
        $topPackages = $this->packageMarkdownService->getTopPackages(10);

        return new PackageIndexData(
            packages: $packages,
            prodPackages: $prodPackages,
            devPackages: $devPackages,
            topPackages: $topPackages
        );
    }
}
