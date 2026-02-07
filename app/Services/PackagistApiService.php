<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PackagistApiService
{
    /**
     * The base URL for the Packagist API.
     */
    protected string $apiUrl = 'https://packagist.org/api';

    /**
     * The cache duration in minutes.
     */
    protected int $cacheDuration = 60;

    /**
     * Get package details from the Packagist API.
     */
    public function getPackageDetails(string $packageName): ?array
    {
        $cacheKey = "packagist_package_{$packageName}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($packageName) {
            try {
                $response = Http::get("{$this->apiUrl}/packages/{$packageName}.json");

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                logger()->error('Failed to fetch package details from Packagist', [
                    'package' => $packageName,
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        });
    }

    /**
     * Get package download statistics from the Packagist API.
     */
    public function getPackageDownloads(string $packageName): ?array
    {
        $cacheKey = "packagist_downloads_{$packageName}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($packageName) {
            try {
                $response = Http::get("{$this->apiUrl}/packages/{$packageName}/stats.json");

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                logger()->error('Failed to fetch package download statistics from Packagist', [
                    'package' => $packageName,
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        });
    }

    /**
     * Get the latest version of a package.
     */
    public function getLatestVersion(string $packageName): ?string
    {
        $details = $this->getPackageDetails($packageName);

        if (! $details || ! isset($details['package']['versions'])) {
            return null;
        }

        $versions = $details['package']['versions'];

        // Filter out dev versions
        $stableVersions = array_filter($versions, function ($version) {
            return ! str_contains($version, 'dev');
        }, ARRAY_FILTER_USE_KEY);

        if (empty($stableVersions)) {
            return null;
        }

        // Sort versions by time
        uasort($stableVersions, function ($a, $b) {
            return strtotime($b['time'] ?? '0') <=> strtotime($a['time'] ?? '0');
        });

        // Get the first (latest) version
        $latestVersion = array_key_first($stableVersions);

        return $latestVersion;
    }

    /**
     * Check if a package has a newer version available.
     */
    public function hasNewerVersion(string $packageName, string $currentVersion): bool
    {
        $latestVersion = $this->getLatestVersion($packageName);

        if (! $latestVersion) {
            return false;
        }

        // Remove the version constraint characters (^, ~, etc.)
        $currentVersion = preg_replace('/[^0-9.]/', '', $currentVersion);
        $latestVersion = preg_replace('/[^0-9.]/', '', $latestVersion);

        return version_compare($latestVersion, $currentVersion, '>');
    }

    /**
     * Get the GitHub repository URL for a package.
     */
    public function getGitHubRepositoryUrl(string $packageName): ?string
    {
        $details = $this->getPackageDetails($packageName);

        if (! $details || ! isset($details['package']['repository'])) {
            return null;
        }

        $repository = $details['package']['repository'];

        // Check if it's a GitHub repository
        if (str_contains($repository, 'github.com')) {
            return $repository;
        }

        return null;
    }

    /**
     * Get the maintainers of a package.
     */
    public function getMaintainers(string $packageName): array
    {
        $details = $this->getPackageDetails($packageName);

        if (! $details || ! isset($details['package']['maintainers'])) {
            return [];
        }

        return $details['package']['maintainers'];
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

    /**
     * Clear the cache for a package.
     */
    public function clearCache(string $packageName): void
    {
        Cache::forget("packagist_package_{$packageName}");
        Cache::forget("packagist_downloads_{$packageName}");
    }
}
