<?php

namespace App\Services;

use App\Models\ComposerPackage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackageAssetsService
{
    /**
     * Get the logo for a package.
     *
     * @param  ComposerPackage|string  $package
     */
    public function getPackageLogo($package): ?array
    {
        if (is_string($package)) {
            $package = ComposerPackage::findByName($package);
        }

        if (! $package) {
            return null;
        }

        return $package->logo;
    }

    /**
     * Get the README HTML for a package.
     *
     * @param  ComposerPackage|string  $package
     */
    public function getPackageReadmeHtml($package): ?string
    {
        if (is_string($package)) {
            $package = ComposerPackage::findByName($package);
        }

        if (! $package) {
            return null;
        }

        // Find the README.md file
        $readmeFile = null;
        foreach ($package->getMarkdownFiles() as $file) {
            if (strtolower($file['path']) === 'readme.md') {
                $readmeFile = $file;
                break;
            }
        }

        if (! $readmeFile) {
            return null;
        }

        return $package->getMarkdownHtml('readme.md');
    }

    /**
     * Create a placeholder logo for a package.
     */
    public function createPlaceholderLogo(string $packageName): array
    {
        $placeholderPath = 'packages/'.$packageName.'/placeholder.svg';
        $placeholderContent = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="#f0f0f0"/><text x="50" y="50" font-family="Arial" font-size="12" text-anchor="middle" alignment-baseline="middle" fill="#999">No Image</text></svg>';
        Storage::disk('public')->put($placeholderPath, $placeholderContent);

        return [
            'path' => null,
            'storage_path' => $placeholderPath,
            'url' => Storage::disk('public')->url($placeholderPath),
        ];
    }

    /**
     * Extract the first image URL from markdown content.
     */
    public function extractImageFromMarkdown(string $markdown): ?string
    {
        // Match Markdown image syntax: ![alt text](image-url)
        if (preg_match('/!\[.*?\]\((.*?)\)/', $markdown, $matches)) {
            return $matches[1];
        }

        // Match HTML image tags: <img src="image-url" />
        if (preg_match('/<img.*?src=["\'](.*?)["\'].*?>/', $markdown, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Download and store an image from a URL.
     */
    public function downloadAndStoreImage(string $url, string $packageName, string $storagePath): ?array
    {
        try {
            $imageContent = @file_get_contents($url);
            if ($imageContent) {
                Storage::disk('public')->put($storagePath, $imageContent);

                return [
                    'path' => null,
                    'storage_path' => $storagePath,
                    'url' => Storage::disk('public')->url($storagePath),
                ];
            }
        } catch (\Exception $e) {
            // If there's an error downloading, return null
            return null;
        }

        return null;
    }

    /**
     * Process a README file to extract and store images.
     */
    public function processReadmeForImages(ComposerPackage $package, array $readmeFile): ?array
    {
        $imageUrl = $this->extractImageFromMarkdown($readmeFile['content']);

        if (! $imageUrl) {
            return null;
        }

        // Check if the URL is relative or absolute
        if (Str::startsWith($imageUrl, ['http://', 'https://'])) {
            // It's an absolute URL, try to download the image
            $imageBasename = basename($imageUrl);
            $readmeDir = dirname($readmeFile['path']);
            $storagePath = 'packages/'.$package->name.'/'.$readmeDir.'/images/'.$imageBasename;

            return $this->downloadAndStoreImage($imageUrl, $package->name, $storagePath);
        } else {
            // It's a relative URL
            if (Str::startsWith($imageUrl, '/')) {
                // It's relative to the root of the site, create a placeholder
                return $this->createPlaceholderLogo($package->name);
            }

            // It's relative to the current directory, check if the file exists in the vendor directory
            $readmeDir = dirname($readmeFile['relative_path']);
            $relativeImagePath = $imageUrl;
            $imageFullPath = base_path($readmeDir.'/'.$relativeImagePath);

            if (file_exists($imageFullPath)) {
                // The image exists, create a storage path
                $storagePath = 'packages/'.$package->name.'/'.dirname($readmeFile['path']).'/'.$relativeImagePath;

                // Copy the image to the public storage disk
                $imageContent = file_get_contents($imageFullPath);
                Storage::disk('public')->put($storagePath, $imageContent);

                return [
                    'path' => 'vendor/'.$package->name.'/'.dirname($readmeFile['path']).'/'.$relativeImagePath,
                    'storage_path' => $storagePath,
                    'url' => Storage::disk('public')->url($storagePath),
                ];
            }
        }

        return null;
    }
}
