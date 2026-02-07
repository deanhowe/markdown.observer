<?php

namespace App\Services;

use App\Events\ComposerPackagesAnalyzed;
use App\Facades\ComposerPackages;
use App\Repositories\ComposerPackagesRepository;
use App\Services\MarkdownService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use RuntimeException;
#use Illuminate\Support\Facades\Process;

class ComposerPackagesService
{
    public function __construct(
        private readonly ComposerPackagesRepository $repository,
        private readonly MarkdownService $markdownService,
        private readonly string $cachePrefix = 'composer_packages',
        private readonly int $cacheTtl = 3600
    ) {}

    public function analyze(bool $includeReadmeHtml = false): array
    {
        $packages = $this->repository->getDependencies();

        // Add logo information and optionally README HTML to each package
        $packagesWithExtras = $packages->map(function ($package) use ($includeReadmeHtml) {
            $packageArray = $package->toArray();
            $packageArray['logo'] = $this->getPackageLogo($package->name);

            // Add README HTML if requested
            if ($includeReadmeHtml) {
                $packageArray['readme_html'] = $this->getPackageReadme($package->name, true);
            }

            return $packageArray;
        })->toArray();

        Event::dispatch(new ComposerPackagesAnalyzed($packagesWithExtras));

        return $packagesWithExtras;
    }

    public function getCached(bool $includeReadmeHtml = false): array
    {
        $cacheKey = $includeReadmeHtml
            ? "{$this->cachePrefix}_analysis_with_readme"
            : "{$this->cachePrefix}_analysis";

        return Cache::remember(
            $cacheKey,
            $this->cacheTtl,
            fn () => $this->analyze($includeReadmeHtml)
        );
    }

    /**
     * Get paginated packages
     *
     * @param int $page
     * @param int $perPage
     * @param bool $includeReadmeHtml
     * @return array
     */
    public function getPaginated(int $page = 1, int $perPage = 10, bool $includeReadmeHtml = false): array
    {
        $cacheKey = "{$this->cachePrefix}_analysis" . ($includeReadmeHtml ? "_with_readme" : "");

        // Get all packages from cache or analyze them
        $allPackages = Cache::remember(
            $cacheKey,
            $this->cacheTtl,
            fn () => $this->analyze($includeReadmeHtml)
        );

        // Calculate pagination
        $total = count($allPackages);
        $lastPage = max(1, ceil($total / $perPage));
        $page = min($page, $lastPage);

        // Get the slice of packages for the current page
        $offset = ($page - 1) * $perPage;
        $items = array_slice($allPackages, $offset, $perPage);

        // Return paginated result
        return [
            'data' => $items,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'total' => $total,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ]
        ];
    }

    public function invalidateCache(): void
    {
        // Forget analysis cache
        Cache::forget("{$this->cachePrefix}_analysis");
        Cache::forget("{$this->cachePrefix}_analysis_with_readme");

        // Forget package-specific caches
        $packages = $this->repository->getPackages();
        foreach ($packages as $package) {
            $packageName = $package['name'];

            // Forget logo cache
            Cache::forget("{$this->cachePrefix}_logo_{$packageName}");

            // Forget README caches
            Cache::forget("{$this->cachePrefix}_readme_{$packageName}");
            Cache::forget("{$this->cachePrefix}_readme_{$packageName}_html");
        }
    }

    /**
     * Get the logo URI for a package
     *
     * @param string $packageName
     * @return array|null
     */
    public function getPackageLogo(string $packageName): ?array
    {
        // Use cache to avoid repeated processing
        $cacheKey = "{$this->cachePrefix}_logo_{$packageName}";

        return Cache::remember(
            $cacheKey,
            $this->cacheTtl,
            function () use ($packageName) {
                // Check if the package exists in the vendor directory
                $packagePath = 'vendor/' . $packageName;
                if (!Storage::disk('local')->exists($packagePath)) {
                    return null;
                }

                // First, look for logo files in the art directory or named 'logo'
                $logoFiles = $this->findLogoFiles($packagePath);
                if (!empty($logoFiles)) {
                    $logoFile = $logoFiles[0]; // Use the first logo file found
                    return $this->createLogoData($packageName, $logoFile);
                }

                // If no logo files found, look for images in README.md
                $readmeFiles = $this->findReadmeFiles($packagePath);
                foreach ($readmeFiles as $readmeFile) {
                    $relativePath = str_replace(Storage::disk('local')->path(''), '', $readmeFile);
                    $content = Storage::disk('local')->get($relativePath);
                    $imageUrl = $this->extractImageFromMarkdown($content);
                    if ($imageUrl) {
                        return $this->createLogoDataFromUrl($packageName, $imageUrl, dirname($readmeFile));
                    }
                }

                // If no logo found, create a placeholder
                return $this->createPlaceholderLogo($packageName);
            }
        );
    }

    /**
     * Find logo files in a package directory
     *
     * @param string $packagePath
     * @return array
     */
    private function findLogoFiles(string $packagePath): array
    {
        $logoFiles = [];
        $imageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'svg'];

        // Use Storage to recursively get all files
        $allFiles = Storage::disk('local')->allFiles($packagePath);

        foreach ($allFiles as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            // Check if it's an image file
            if (in_array($extension, $imageExtensions)) {
                // Check if it's in the art directory or named logo
                if (Str::contains($file, '/art/') || Str::contains(strtolower(basename($file)), 'logo')) {
                    $logoFiles[] = Storage::disk('local')->path($file);
                }
            }
        }

        return $logoFiles;
    }

    /**
     * Find README.md files in a package directory
     *
     * @param string $packagePath
     * @return array
     */
    private function findReadmeFiles(string $packagePath): array
    {
        $readmeFiles = [];

        // Use Storage to recursively get all files
        $allFiles = Storage::disk('local')->allFiles($packagePath);

        foreach ($allFiles as $file) {
            if (strtolower(basename($file)) === 'readme.md') {
                $readmeFiles[] = Storage::disk('local')->path($file);
            }
        }

        return $readmeFiles;
    }

    /**
     * Extract the first image URL from markdown content
     *
     * @param string $markdown
     * @return string|null
     */
    private function extractImageFromMarkdown(string $markdown): ?string
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
     * Convert an image to a base64 data URI
     *
     * @param string $content The image content
     * @param string $extension The image file extension
     * @return string
     */
    private function convertToBase64DataUri(string $content, string $extension): string
    {
        $base64 = base64_encode($content);
        $mimeType = $this->getMimeTypeFromExtension($extension);
        return "data:{$mimeType};base64,{$base64}";
    }

    /**
     * Get MIME type from file extension
     *
     * @param string $extension
     * @return string
     */
    private function getMimeTypeFromExtension(string $extension): string
    {
        $extension = strtolower($extension);
        $mimeTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Create logo data from a logo file
     *
     * @param string $packageName
     * @param string $logoFile
     * @return array
     */
    private function createLogoData(string $packageName, string $logoFile): array
    {
        $relativePath = str_replace(Storage::disk('local')->path(''), '', $logoFile);
        $extension = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));

        // Read the file content
        $content = Storage::disk('local')->get($relativePath);

        // Convert to base64 data URI
        $dataUri = $this->convertToBase64DataUri($content, $extension);

        return [
            'path' => $relativePath,
            'data_uri' => $dataUri,
        ];
    }

    /**
     * Create logo data from a URL
     *
     * @param string $packageName
     * @param string $imageUrl
     * @param string $baseDir
     * @return array
     */
    private function createLogoDataFromUrl(string $packageName, string $imageUrl, string $baseDir): array
    {
        // Check if the URL is relative or absolute
        if (Str::startsWith($imageUrl, ['http://', 'https://'])) {
            // It's an absolute URL, try to download the image
            $imageBasename = basename($imageUrl);
            $extension = strtolower(pathinfo($imageBasename, PATHINFO_EXTENSION));

            // Try to download the image
            try {
                $imageContent = @file_get_contents($imageUrl);
                if ($imageContent) {
                    // Convert to base64 data URI
                    $dataUri = $this->convertToBase64DataUri($imageContent, $extension);
                    return [
                        'path' => null,
                        'data_uri' => $dataUri,
                    ];
                }
            } catch (\Exception $e) {
                // If there's an error downloading, return a placeholder
                return $this->createPlaceholderLogo($packageName);
            }
        } else {
            // It's a relative URL
            if (Str::startsWith($imageUrl, '/')) {
                // It's relative to the root of the site, return a placeholder
                return $this->createPlaceholderLogo($packageName);
            }

            // It's relative to the current directory, check if the file exists
            $imageFullPath = $baseDir . '/' . $imageUrl;
            $relativePath = str_replace(Storage::disk('local')->path(''), '', $imageFullPath);
            if (Storage::disk('local')->exists($relativePath)) {
                $extension = strtolower(pathinfo($imageFullPath, PATHINFO_EXTENSION));

                // Read the image content
                $imageContent = Storage::disk('local')->get($relativePath);

                // Convert to base64 data URI
                $dataUri = $this->convertToBase64DataUri($imageContent, $extension);

                return [
                    'path' => $relativePath,
                    'data_uri' => $dataUri,
                ];
            }
        }

        // If we couldn't get a logo, return a placeholder
        return $this->createPlaceholderLogo($packageName);
    }

    /**
     * Create a placeholder logo
     *
     * @param string $packageName
     * @return array
     */
    private function createPlaceholderLogo(string $packageName): array
    {
        $placeholderContent = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="#f0f0f0"/><text x="50" y="50" font-family="Arial" font-size="12" text-anchor="middle" alignment-baseline="middle" fill="#999">No Image</text></svg>';

        // Convert to base64 data URI
        $dataUri = $this->convertToBase64DataUri($placeholderContent, 'svg');

        return [
            'path' => null,
            'data_uri' => $dataUri,
        ];
    }

    /**
     * Get the README.md content for a package and convert it to HTML
     *
     * @param string $packageName
     * @param bool $includeHtml Whether to convert the README to HTML
     * @return string|null
     */
    public function getPackageReadme(string $packageName, bool $includeHtml = false): ?string
    {
        // Use cache to avoid repeated processing
        $cacheKey = "{$this->cachePrefix}_readme_{$packageName}" . ($includeHtml ? "_html" : "");

        return Cache::remember(
            $cacheKey,
            $this->cacheTtl,
            function () use ($packageName, $includeHtml) {
                // Check if the package exists in the vendor directory
                $packagePath = 'vendor/' . $packageName;
                if (!Storage::disk('local')->exists($packagePath)) {
                    return null;
                }

                // Find README.md files
                $readmeFiles = $this->findReadmeFiles($packagePath);
                if (empty($readmeFiles)) {
                    return null;
                }

                // Get the content of the first README.md file
                $relativePath = str_replace(Storage::disk('local')->path(''), '', $readmeFiles[0]);
                $readmeContent = Storage::disk('local')->get($relativePath);

                // If HTML conversion is not requested, return the raw markdown
                if (!$includeHtml) {
                    return $readmeContent;
                }

                // Convert markdown to HTML
                return $this->markdownService->toHtml($readmeContent);
            }
        );
    }
}
