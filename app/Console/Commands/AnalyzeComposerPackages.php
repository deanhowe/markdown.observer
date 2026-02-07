<?php

namespace App\Console\Commands;

use App\Contracts\ComposerPackages;
use App\Services\ComposerPackageAnalysisService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AnalyzeComposerPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:analyze-composer-packages {--queue : Run the analysis in the background using the queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze composer packages and their usage in the application';

    /**
     * The composer packages repository.
     */
    protected ComposerPackages $composerPackagesRepository;

    /**
     * The composer package analysis service.
     */
    protected ComposerPackageAnalysisService $analysisService;

    /**
     * Create a new command instance.
     */
    public function __construct(
        ComposerPackages $composerPackagesRepository,
        ComposerPackageAnalysisService $analysisService
    ) {
        parent::__construct();
        $this->composerPackagesRepository = $composerPackagesRepository;
        $this->analysisService = $analysisService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if we should use the queue
        if ($this->option('queue') && config('queue.default') !== 'sync') {
            $this->info('Dispatching package analysis job to the queue...');
            \App\Jobs\AnalyzeComposerPackagesJob::dispatch();
            $this->info('Job dispatched. Analysis will run in the background.');

            return 0;
        }

        $this->info('Analyzing composer packages...');

        // Get all installed packages from composer repository
        $dependencies = $this->composerPackagesRepository->getDependencies();

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
        $this->info('Analyzing package usage in the codebase...');
        $this->analysisService->analyzeUsage($packageData);

        // Clone the vendor directory structure, but only include images and markdown files
        $this->info('Cloning vendor directory structure (images and markdown files only)...');
        $this->analysisService->cloneVendorDirectory($packageData);

        // Enrich package data with information from Packagist API
        $this->info('Enriching package data with information from Packagist API...');
        $this->analysisService->enrichPackageData($packageData);

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
        $checksum = $this->composerPackagesRepository->getChecksum();
        Storage::disk('local')->put(
            $outputDir.'/composer-checksum.txt',
            $checksum
        );

        // Clear any caches that might be using the old data
        if (app()->bound('App\Services\PackageCacheService')) {
            app('App\Services\PackageCacheService')->clearCache();
        }

        $this->info('Analysis complete. Data saved to '.$outputPath);
    }
}
