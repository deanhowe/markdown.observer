<?php

namespace App\Services;

use App\Contracts\ComposerPackages;
use App\Models\ComposerPackage;
use Illuminate\Support\Facades\Cache;

class PackageCacheService
{
    /**
     * The composer packages repository.
     */
    protected ComposerPackages $composerPackagesRepository;

    /**
     * The cache duration in minutes.
     */
    protected int $cacheDuration;

    /**
     * Create a new service instance.
     */
    public function __construct(ComposerPackages $composerPackagesRepository, int $cacheDuration = 60)
    {
        $this->composerPackagesRepository = $composerPackagesRepository;
        $this->cacheDuration = $cacheDuration;
    }

    /**
     * Get all packages from cache or refresh if needed.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPackages()
    {
        $cacheKey = 'composer_packages_all';

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            if ($this->needsRefresh()) {
                $this->refreshPackageData();
            }

            return ComposerPackage::all();
        });
    }

    /**
     * Get the top N most used packages from cache.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopPackages(int $limit = 10)
    {
        $cacheKey = "composer_packages_top_{$limit}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($limit) {
            if ($this->needsRefresh()) {
                $this->refreshPackageData();
            }

            return ComposerPackage::getTopPackages($limit);
        });
    }

    /**
     * Get packages with logos from cache.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPackagesWithLogos(int $limit = 10)
    {
        $cacheKey = "composer_packages_with_logos_{$limit}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($limit) {
            if ($this->needsRefresh()) {
                $this->refreshPackageData();
            }

            return ComposerPackage::getPackagesWithLogos($limit);
        });
    }

    /**
     * Get packages of a specific type from cache.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPackagesByType(string $type)
    {
        $cacheKey = "composer_packages_type_{$type}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($type) {
            if ($this->needsRefresh()) {
                $this->refreshPackageData();
            }

            return ComposerPackage::getPackagesByType($type);
        });
    }

    /**
     * Find a package by name from cache.
     */
    public function findPackageByName(string $name): ?ComposerPackage
    {
        $cacheKey = "composer_package_{$name}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($name) {
            if ($this->needsRefresh()) {
                $this->refreshPackageData();
            }

            return ComposerPackage::findByName($name);
        });
    }

    /**
     * Get the README HTML for a package from cache.
     */
    public function getPackageReadmeHtml(string $packageName): ?string
    {
        $cacheKey = "composer_package_readme_{$packageName}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($packageName) {
            $package = $this->findPackageByName($packageName);
            if (! $package) {
                return null;
            }

            // Find the README.md file
            foreach ($package->getMarkdownFiles() as $file) {
                if (strtolower($file['path']) === 'readme.md') {
                    return $package->getMarkdownHtml('readme.md');
                }
            }

            return null;
        });
    }

    /**
     * Get a specific markdown file for a package from cache.
     */
    public function getPackageMarkdownFile(string $packageName, string $filePath): ?array
    {
        $cacheKey = "composer_package_markdown_file_{$packageName}_{$filePath}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($packageName, $filePath) {
            $package = $this->findPackageByName($packageName);
            if (! $package) {
                return null;
            }

            return $package->getMarkdownFile($filePath);
        });
    }

    /**
     * Get the content of a specific markdown file for a package from cache.
     */
    public function getPackageMarkdownContent(string $packageName, string $filePath): ?string
    {
        $cacheKey = "composer_package_markdown_content_{$packageName}_{$filePath}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($packageName, $filePath) {
            $package = $this->findPackageByName($packageName);
            if (! $package) {
                return null;
            }

            return $package->getMarkdownContent($filePath);
        });
    }

    /**
     * Get the HTML content of a markdown file for a package from cache.
     */
    public function getPackageMarkdownHtml(string $packageName, string $filePath): ?string
    {
        $cacheKey = "composer_package_markdown_html_{$packageName}_{$filePath}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($packageName, $filePath) {
            $package = $this->findPackageByName($packageName);
            if (! $package) {
                return null;
            }

            return $package->getMarkdownHtml($filePath);
        });
    }

    /**
     * Get the directory tree of markdown files for a package from cache.
     */
    public function getPackageMarkdownDirectoryTree(string $packageName): ?array
    {
        $cacheKey = "composer_package_markdown_directory_tree_{$packageName}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($packageName) {
            $package = $this->findPackageByName($packageName);
            if (! $package) {
                return null;
            }

            return $package->markdown_directory_tree;
        });
    }

    /**
     * Check if the package data needs to be refreshed.
     */
    public function needsRefresh(): bool
    {
        return ComposerPackage::needsRefresh($this->composerPackagesRepository);
    }

    /**
     * Refresh the package data.
     */
    public function refreshPackageData(): bool
    {
        $result = ComposerPackage::refreshData();

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Clear all package-related caches.
     */
    public function clearCache(): void
    {
        Cache::forget('composer_packages_all');

        // Clear top packages caches
        for ($i = 1; $i <= 50; $i++) {
            Cache::forget("composer_packages_top_{$i}");
        }

        // Clear packages with logos caches
        for ($i = 1; $i <= 50; $i++) {
            Cache::forget("composer_packages_with_logos_{$i}");
        }

        // Clear package type caches
        Cache::forget('composer_packages_type_prod');
        Cache::forget('composer_packages_type_dev');

        // Clear individual package caches
        $packages = ComposerPackage::all();
        foreach ($packages as $package) {
            // Clear basic package cache
            Cache::forget("composer_package_{$package->name}");

            // Clear README cache
            Cache::forget("composer_package_readme_{$package->name}");

            // Clear directory tree cache
            Cache::forget("composer_package_markdown_directory_tree_{$package->name}");

            // Clear individual markdown file caches
            foreach ($package->getMarkdownFiles() as $file) {
                $filePath = $file['path'];
                Cache::forget("composer_package_markdown_file_{$package->name}_{$filePath}");
                Cache::forget("composer_package_markdown_content_{$package->name}_{$filePath}");
                Cache::forget("composer_package_markdown_html_{$package->name}_{$filePath}");
            }
        }
    }

    /**
     * Set the cache duration.
     */
    public function setCacheDuration(int $minutes): self
    {
        $this->cacheDuration = $minutes;

        return $this;
    }

    /**
     * Get the cache duration.
     */
    public function getCacheDuration(): int
    {
        return $this->cacheDuration;
    }
}
