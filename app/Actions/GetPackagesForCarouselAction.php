<?php

namespace App\Actions;

use App\Contracts\ComposerPackages;
use App\DataTransferObjects\ComposerPackageData;
use App\Models\ComposerPackage;

class GetPackagesForCarouselAction
{
    public function __construct(
        private readonly ComposerPackages $composerPackagesRepository
    ) {}

    /**
     * Execute the action to get packages for the carousel.
     *
     * @param  int|null  $limit  The maximum number of packages to return. Pass 0 or null for all.
     * @param  bool  $includeReadme  Whether to include README HTML in the payload (heavy). Defaults to true for BC.
     * @param  string  $orderBy  Field to order by: rank|name|usage_count|type
     * @param  string  $direction  asc|desc
     * @param  string  $type  all|prod|dev
     */
    public function execute(?int $limit = 10, bool $includeReadme = true, string $orderBy = 'rank', string $direction = 'asc', string $type = 'all'): array
    {
        // Check if we need to refresh the package data
        if (ComposerPackage::needsRefresh($this->composerPackagesRepository)) {
            ComposerPackage::refreshData();
        }

        // Get packages with logos using the Sushi model (with filters & ordering)
        $packages = ComposerPackage::getPackagesWithLogos($limit ?? 0, $type, $orderBy, $direction);

        $packagesWithReadme = [];

        foreach ($packages as $package) {
            // Find the README.md file
            $readmeFile = null;
            foreach ($package->markdown_files ?? [] as $file) {
                if (strtolower($file['path']) === 'readme.md') {
                    $readmeFile = $file;
                    break;
                }
            }

            // Get README HTML directly from the model
            $readmeHtml = '';
            if ($includeReadme && $readmeFile && isset($readmeFile['html'])) {
                $readmeHtml = $readmeFile['html'];
            }

            // Create a DTO for the package
            $packagesWithReadme[] = ComposerPackageData::fromArrayWithValidation([
                'name' => $package->name,
                'version' => $package->version ?? 'unknown',
                'description' => $package->description ?? '',
                'homepage' => $package->homepage ?? null,
                'directDependency' => $package['direct-dependency'] ?? null,
                'source' => $package->source ?? null,
                'abandoned' => $package->abandoned ?? null,
                'dependencies' => $package->dependencies ?? [],
                'isDev' => ($package->type ?? 'unknown') === 'dev',
                'logo' => $package->logo ?? null,
                'readmeHtml' => $readmeHtml,
                'markdownDirectoryTree' => $package->markdown_directory_tree ?? null,
                'rank' => $package->rank ?? null,
                'type' => $package->type ?? null,
            ]);
        }

        // Convert the ComposerPackageData objects to arrays
        $packagesArray = array_map(function (ComposerPackageData $package) use ($includeReadme) {
            $arr = $package->toArray();
            if (! $includeReadme) {
                // Keep response shape stable but omit heavy content
                $arr['readme_html'] = '';
            }

            return $arr;
        }, $packagesWithReadme);

        return $packagesArray;
    }
}
