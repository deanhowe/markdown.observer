<?php

namespace App\Jobs;

use App\Contracts\ComposerPackages;
use App\Services\ComposerPackageAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class AnalyzeComposerPackagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ComposerPackages $composerPackagesRepository, ComposerPackageAnalysisService $analysisService)
    {
        // Get all installed packages from composer repository
        $dependencies = $composerPackagesRepository->getDependencies();

        // Initialize package data
        $packageData = [];
        foreach ($dependencies as $package) {
            $packageData[$package->name] = [
                'name' => $package->name,
                'version' => $package->version,
                'type' => $package->isDev ? 'dev' : 'prod',
                'usage_count' => 0,
                'files' => [],
                'markdown_files' => [],
                'logo' => null,
                'description' => $package->description,
                'homepage' => $package->homepage,
                'direct-dependency' => $package->directDependency,
                'source' => $package->source,
                'abandoned' => $package->abandoned,
                'dependencies' => $package->dependencies,
            ];
        }

        // Analyze usage in the codebase
        $analysisService->analyzeUsage($packageData);

        // Clone the vendor directory structure, but only include images and markdown files
        $analysisService->cloneVendorDirectory($packageData);

        // Enrich package data with information from Packagist API
        $analysisService->enrichPackageData($packageData);

        // Sort packages by usage count
        uasort($packageData, function ($a, $b) {
            return $b['usage_count'] <=> $a['usage_count'];
        });

        // Add rank to each package
        $rank = 1;
        foreach ($packageData as &$data) {
            $data['rank'] = $rank++;
        }

        // Ensure the directory exists (use disk-relative paths so readers find the files)
        $outputDir = 'database/data';
        if (! Storage::disk('local')->exists($outputDir)) {
            Storage::disk('local')->makeDirectory($outputDir, 0755, true);
        }

        // Save the data to composer-details.json
        $outputPath = $outputDir.'/composer-details.json';
        Storage::disk('local')->put(
            $outputPath,
            json_encode($packageData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // Add the composer.json checksum to the data
        $checksum = $composerPackagesRepository->getChecksum();
        Storage::disk('local')->put(
            $outputDir.'/composer-checksum.txt',
            $checksum
        );

        // Clear any caches that might be using the old data
        if (app()->bound('App\Services\PackageCacheService')) {
            app('App\Services\PackageCacheService')->clearCache();
        }
    }
}
