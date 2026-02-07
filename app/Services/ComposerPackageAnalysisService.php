<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ComposerPackageAnalysisService
{
    /**
     * The Packagist API service.
     */
    protected ?PackagistApiService $packagistApiService = null;

    /**
     * Create a new service instance.
     */
    public function __construct(?PackagistApiService $packagistApiService = null)
    {
        $this->packagistApiService = $packagistApiService;
    }

    /**
     * Analyze the usage of packages in the codebase.
     */
    public function analyzeUsage(array &$packageData)
    {
        // Get all PHP files in the project
        $phpFiles = $this->getAllPhpFiles();

        // Prepare package search patterns for more efficient searching
        $packagePatterns = [];
        foreach ($packageData as $package => $_) {
            // Convert package name to namespace format
            $packageParts = explode('/', $package);
            if (count($packageParts) === 2) {
                $vendor = Str::studly($packageParts[0]);
                $name = Str::studly($packageParts[1]);
                $namespace = "$vendor\\$name";

                $packagePatterns[$package] = [
                    'namespace' => $namespace,
                    'vendor' => $vendor,
                    'direct' => str_replace(['/', '-'], ['\\', ''], $package),
                ];
            } else {
                $packagePatterns[$package] = [
                    'direct' => str_replace(['/', '-'], ['\\', ''], $package),
                ];
            }
        }

        // Process files in chunks to reduce memory usage
        $chunkSize = 50;
        $fileChunks = array_chunk($phpFiles, $chunkSize);

        foreach ($fileChunks as $chunk) {
            $this->processFileChunk($chunk, $packageData, $packagePatterns);
        }

        // Remove duplicates from files array more efficiently
        foreach ($packageData as &$data) {
            if (! isset($data['files']) || ! is_array($data['files'])) {
                $data['files'] = [];

                continue;
            }

            // Use associative array for faster duplicate checking
            $uniqueFiles = [];
            foreach ($data['files'] as $file) {
                if (is_array($file) && isset($file['path'])) {
                    $uniqueFiles[$file['path']] = $file;
                }
            }

            $data['files'] = array_values($uniqueFiles);
        }
    }

    /**
     * Process a chunk of files to analyze package usage.
     */
    private function processFileChunk(array $files, array &$packageData, array $packagePatterns): void
    {
        foreach ($files as $file) {
            // Get the relative path
            $relativePath = $this->getRelativePath($file);

            // Read the file content
            try {
                $content = File::get($file);
            } catch (\Exception $e) {
                logger()->warning("Could not read file: $file");

                continue;
            }

            // Create PHPStorm URL once per file
            $phpStormUrl = $this->getPhpStormUrl($relativePath);

            foreach ($packagePatterns as $package => $patterns) {
                $found = false;

                // Check for namespace usage if available
                if (isset($patterns['namespace'])) {
                    $namespace = $patterns['namespace'];
                    $vendor = $patterns['vendor'];

                    if (Str::contains($content, "use $namespace") ||
                        Str::contains($content, "use $vendor\\") ||
                        Str::contains($content, "$namespace\\")) {
                        $found = true;
                    }
                }

                // Check for direct class usage
                if (! $found && Str::contains($content, $patterns['direct'])) {
                    $found = true;
                }

                if ($found) {
                    $packageData[$package]['usage_count'] = ($packageData[$package]['usage_count'] ?? 0) + 1;

                    if (! isset($packageData[$package]['files'])) {
                        $packageData[$package]['files'] = [];
                    }

                    // Add file to the package's files array
                    $packageData[$package]['files'][] = [
                        'path' => $relativePath,
                        'url' => $phpStormUrl,
                    ];
                }
            }
        }
    }

    /**
     * Clone the vendor directory structure, but only include images and markdown files.
     */
    public function cloneVendorDirectory(array &$packageData)
    {
        foreach ($packageData as $package => &$data) {
            // Check if the package exists in the vendor directory
            $packagePath = 'vendor/'.$package;
            if (File::exists(base_path($packagePath))) {
                // Process all files in the package directory
                $this->processPackageDirectory($package, $data);
            }
        }
    }

    /**
     * Process all files in a package directory.
     */
    private function processPackageDirectory(string $package, array &$packageData)
    {
        $packagePath = 'vendor/'.$package;
        $basePath = base_path($packagePath);

        // Get all files in the package directory recursively
        $files = $this->getAllFilesInDirectory($basePath, $packagePath);

        // First pass: Process markdown files and collect README.md files
        $readmeFiles = [];
        $markdownPaths = [];
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION));
            $filePath = $file['path'];
            $relativePath = $file['relative_path'];

            // Process markdown files
            if (in_array($extension, ['md', 'markdown'])) {
                $fullRelativePath = $relativePath;
                $phpStormUrl = $this->getPhpStormUrl($fullRelativePath);

                // Create a storage path that mirrors the vendor directory structure
                $storagePath = 'packages/'.$package.'/'.str_replace($packagePath.'/', '', $relativePath);

                // Copy the file to the public storage disk
                $content = File::get($filePath);
                Storage::disk('public')->put($storagePath, $content);

                // Get the relative path without the package path prefix
                $relativePathWithoutPackage = str_replace($packagePath.'/', '', $relativePath);

                // Add to the markdown_files array in package data
                $packageData['markdown_files'][] = [
                    'path' => $relativePathWithoutPackage,
                    'relative_path' => $fullRelativePath,
                    'storage_path' => $storagePath,
                    'url' => $phpStormUrl,
                    'storage_url' => Storage::disk('public')->url($storagePath),
                    'content' => $content,
                    'html' => strtolower(basename($filePath)) === 'readme.md' ? app(\Spatie\LaravelMarkdown\MarkdownRenderer::class)->toHtml($content) : null,
                ];

                // Add to the list of markdown paths for directory tree
                $markdownPaths[] = $relativePathWithoutPackage;

                // Collect README.md files for later processing
                if (strtolower(basename($filePath)) === 'readme.md') {
                    $readmeFiles[] = [
                        'path' => $filePath,
                        'content' => $content,
                        'relativePath' => $relativePathWithoutPackage,
                    ];
                }
            }
        }

        // Build the directory tree of markdown files
        $packageData['markdown_directory_tree'] = $this->buildMarkdownDirectoryTree($markdownPaths);

        // Second pass: Process image files
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION));
            $filePath = $file['path'];
            $relativePath = $file['relative_path'];

            // Process image files
            if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
                $fullRelativePath = $relativePath;

                // Create a storage path that mirrors the vendor directory structure
                $storagePath = 'packages/'.$package.'/'.str_replace($packagePath.'/', '', $relativePath);

                // Copy the file to the public storage disk
                $content = File::get($filePath);
                Storage::disk('public')->put($storagePath, $content);

                // If this is a logo file (in the art directory or named logo), set it as the package logo
                if (Str::contains($filePath, '/art/') || Str::contains(strtolower(basename($filePath)), 'logo')) {
                    $packageData['logo'] = [
                        'path' => $fullRelativePath,
                        'storage_path' => $storagePath,
                        'url' => Storage::disk('public')->url($storagePath),
                    ];
                }
            }
        }

        // Process images from README.md files if no logo was found
        if (! isset($packageData['logo']) || $packageData['logo'] === null) {
            foreach ($readmeFiles as $readmeFile) {
                $imageUrl = $this->extractImageFromMarkdown($readmeFile['content']);

                if ($imageUrl) {
                    // Check if the URL is relative or absolute
                    if (Str::startsWith($imageUrl, ['http://', 'https://'])) {
                        // It's an absolute URL, try to download the image
                        $imageBasename = basename($imageUrl);
                        $readmeDir = dirname($readmeFile['relativePath']);
                        $storagePath = 'packages/'.$package.'/'.$readmeDir.'/images/'.$imageBasename;

                        // Try to download the image and save it to the public storage disk
                        try {
                            $imageContent = @file_get_contents($imageUrl);
                            if ($imageContent) {
                                Storage::disk('public')->put($storagePath, $imageContent);
                                $packageData['logo'] = [
                                    'path' => null,
                                    'storage_path' => $storagePath,
                                    'url' => Storage::disk('public')->url($storagePath),
                                ];
                                break; // Found and processed a logo, no need to continue
                            } else {
                                // If we can't download the image, create a placeholder in storage
                                $this->createPlaceholderLogo($package, $packageData);
                                break;
                            }
                        } catch (\Exception $e) {
                            // If there's an error downloading, create a placeholder in storage
                            $this->createPlaceholderLogo($package, $packageData);
                            break;
                        }
                    } else {
                        // It's a relative URL
                        if (Str::startsWith($imageUrl, '/')) {
                            // It's relative to the root of the site, create a placeholder
                            $this->createPlaceholderLogo($package, $packageData);
                            break; // Found a logo, no need to continue
                        }

                        // It's relative to the current directory, check if the file exists in the vendor directory
                        $readmeDir = dirname($readmeFile['path']);
                        $relativeImagePath = $imageUrl;
                        $imageFullPath = $readmeDir.'/'.$relativeImagePath;

                        if (File::exists($imageFullPath)) {
                            // The image exists, create a storage path
                            $storagePath = 'packages/'.$package.'/'.dirname($readmeFile['relativePath']).'/'.$relativeImagePath;

                            // Copy the image to the public storage disk
                            $imageContent = File::get($imageFullPath);
                            Storage::disk('public')->put($storagePath, $imageContent);
                            $packageData['logo'] = [
                                'path' => 'vendor/'.$package.'/'.dirname($readmeFile['relativePath']).'/'.$relativeImagePath,
                                'storage_path' => $storagePath,
                                'url' => Storage::disk('public')->url($storagePath),
                            ];
                            break; // Found and processed a logo, no need to continue
                        } else {
                            // The image doesn't exist, create a placeholder in storage
                            $this->createPlaceholderLogo($package, $packageData);
                            break; // Found a logo, no need to continue
                        }
                    }
                }
            }
        }
    }

    /**
     * Create a placeholder logo for a package.
     */
    private function createPlaceholderLogo(string $package, array &$packageData): void
    {
        $placeholderPath = 'packages/'.$package.'/placeholder.svg';
        $placeholderContent = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="#f0f0f0"/><text x="50" y="50" font-family="Arial" font-size="12" text-anchor="middle" alignment-baseline="middle" fill="#999">No Image</text></svg>';
        Storage::disk('public')->put($placeholderPath, $placeholderContent);
        $packageData['logo'] = [
            'path' => null,
            'storage_path' => $placeholderPath,
            'url' => Storage::disk('public')->url($placeholderPath),
        ];
    }

    /**
     * Get all files recursively from a directory using File facade.
     */
    public function getAllFilesInDirectory(string $directory, string $relativeTo = ''): array
    {
        $files = [];

        if (! File::exists($directory)) {
            return $files;
        }

        $allFiles = File::allFiles($directory);

        foreach ($allFiles as $file) {
            $path = $file->getPathname();
            $relativePath = $relativeTo ? $relativeTo.'/'.$file->getRelativePathname() : $file->getRelativePathname();

            $files[] = [
                'path' => $path,
                'relative_path' => $relativePath,
            ];
        }

        return $files;
    }

    /**
     * Find all Markdown files in a directory.
     */
    public function findMarkdownFilesInDirectory(string $directory): array
    {
        if (! File::exists($directory)) {
            return [];
        }

        $markdownFiles = [];
        $files = $this->getAllFilesInDirectory($directory, '');

        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION));
            if (in_array($extension, ['md', 'markdown'])) {
                $markdownFiles[] = $file['path'];
            }
        }

        return $markdownFiles;
    }

    /**
     * Get all PHP files in the project.
     */
    public function getAllPhpFiles(): array
    {
        $directories = [
            'app',
            'routes',
            'config',
            'database',
            'resources/views',
            'tests',
        ];

        $files = [];
        foreach ($directories as $directory) {
            try {
                $directoryPath = base_path($directory);
                if (File::exists($directoryPath)) {
                    $directoryFiles = $this->getAllFilesInDirectory($directoryPath, $directory);
                    foreach ($directoryFiles as $file) {
                        $extension = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION));
                        if ($extension === 'php') {
                            $files[] = $file['path'];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Silently continue if there's an error
                continue;
            }
        }

        return $files;
    }

    /**
     * Convert a full path to a path relative to the project root.
     */
    public function getRelativePath(string $fullPath): string
    {
        return Str::replaceFirst(base_path().'/', '', $fullPath);
    }

    /**
     * Generate a PHPStorm URL scheme for a file.
     */
    public function getPhpStormUrl(string $relativePath, int $line = 0): string
    {
        $projectPath = base_path();
        $url = "phpstorm://open?file={$projectPath}/{$relativePath}";

        if ($line > 0) {
            $url .= "&line={$line}";
        }

        return $url;
    }

    /**
     * Find all image files in a directory.
     */
    public function findImageFilesInDirectory(string $directory): array
    {
        if (! File::exists($directory)) {
            return [];
        }

        $imageFiles = [];
        $files = $this->getAllFilesInDirectory($directory, '');

        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION));
            if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
                $imageFiles[] = $file['path'];
            }
        }

        return $imageFiles;
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
     * Build a directory tree structure from an array of file paths.
     *
     * @param  array  $paths  Array of relative file paths
     * @return array Directory tree structure
     */
    private function buildMarkdownDirectoryTree(array $paths): array
    {
        $tree = [];

        foreach ($paths as $path) {
            // Split the path into directories and filename
            $parts = explode('/', $path);
            $filename = array_pop($parts);

            // Start at the root of the tree
            $current = &$tree;

            // Build the directory structure
            foreach ($parts as $part) {
                if (! isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }

            // Add the file to the current directory
            if (! isset($current['files'])) {
                $current['files'] = [];
            }
            $current['files'][] = $filename;
        }

        return $tree;
    }

    /**
     * Enrich package data with information from the Packagist API.
     */
    public function enrichPackageData(array &$packageData): void
    {
        if (! $this->packagistApiService) {
            return;
        }

        foreach ($packageData as $packageName => &$data) {
            // Get package details from Packagist API
            $details = $this->packagistApiService->getPackageDetails($packageName);

            if (! $details) {
                continue;
            }

            // Add package details to the data
            if (isset($details['package'])) {
                $package = $details['package'];

                // Add GitHub repository URL
                if (isset($package['repository'])) {
                    $data['repository'] = $package['repository'];
                }

                // Add maintainers
                if (isset($package['maintainers'])) {
                    $data['maintainers'] = $package['maintainers'];
                }

                // Add latest version
                $latestVersion = $this->packagistApiService->getLatestVersion($packageName);
                if ($latestVersion) {
                    $data['latest_version'] = $latestVersion;

                    // Check if the package has a newer version available
                    $currentVersion = $data['version'];
                    $data['has_newer_version'] = $this->packagistApiService->hasNewerVersion($packageName, $currentVersion);
                }

                // Add download statistics
                $downloads = $this->packagistApiService->getPackageDownloads($packageName);
                if ($downloads && isset($downloads['downloads'])) {
                    $data['downloads'] = $downloads['downloads'];
                }
            }
        }
    }
}
