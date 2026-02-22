<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class ComposerPackageStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole() && !$this->app->runningUnitTests()) {
            return;
        }

        // Register a disk for each composer package
        $this->registerComposerPackageDisks();
    }

    /**
     * Register a disk for each composer package.
     */
    private function registerComposerPackageDisks(): void
    {
        // Get composer.json content
        $composerJsonPath = base_path('composer.json');
        if (!file_exists($composerJsonPath)) {
            return;
        }

        $composerJson = json_decode(file_get_contents($composerJsonPath), true);
        if (!$composerJson) {
            return;
        }

        // Get all packages from require and require-dev
        $packages = array_merge(
            $composerJson['require'] ?? [],
            $composerJson['require-dev'] ?? []
        );

        // Remove php from the list as it's not a package
        unset($packages['php']);

        // Register a disk for each package
        foreach ($packages as $package => $version) {
            $this->registerPackageDisk($package);
        }
    }

    /**
     * Register a disk for a specific composer package.
     */
    private function registerPackageDisk(string $package): void
    {
        // Create a disk name based on the package name
        $diskName = 'package-' . str_replace(['/', '-', '.'], '_', $package);

        // Check if the disk is already defined in the config
        if (config("filesystems.disks.{$diskName}")) {
            // If it's already in the config, we don't need to extend it
            return;
        }

        // Register the disk with the Storage facade
        Storage::extend($diskName, function ($app, $config) use ($package) {
            // Get the base configuration from the composer-packages disk
            $baseConfig = config('filesystems.disks.composer-packages');

            // Override the root to point to the specific package
            $baseConfig['root'] = $baseConfig['root'] . '/' . $package;

            // Create and return the driver
            return $app->make('filesystem')->createLocalDriver($baseConfig);
        });
    }
}
