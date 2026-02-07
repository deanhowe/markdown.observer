<?php

namespace App\Actions;

use App\DataTransferObjects\PackageDetailsData;
use App\Services\PackageMarkdownService;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetPackageDetailsAction
{
    public function __construct(
        private readonly PackageMarkdownService $packageMarkdownService
    ) {}

    /**
     * Execute the action to get details for a specific package.
     *
     * @param string $packageName
     * @return PackageDetailsData
     * @throws HttpResponseException
     */
    public function execute(string $packageName): PackageDetailsData
    {
        $package = $this->packageMarkdownService->getPackage($packageName);

        if (!$package) {
            abort(404, 'Package not found');
        }

        $markdownFiles = $this->packageMarkdownService->getPackageMarkdownFiles($packageName);

        return new PackageDetailsData(
            package: $package,
            markdownFiles: $markdownFiles
        );
    }
}
