<?php

namespace App\Models;

use App\Contracts\ComposerPackages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Sushi\Sushi;

class ComposerPackage extends Model
{
    use Sushi;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'files' => 'array',
        'markdown_files' => 'array',
        'markdown_directory_tree' => 'array',
        'logo' => 'array',
        'dependencies' => 'array',
        'direct-dependency' => 'boolean',
        'abandoned' => 'boolean',
        'usage_count' => 'integer',
        'rank' => 'integer',
        'maintainers' => 'array',
        'downloads' => 'array',
        'has_newer_version' => 'boolean',
    ];

    /**
     * Get the rows for the model.
     *
     * @return array
     */
    public function getRows()
    {
        $dataPath = 'database/data/composer-details.json';
        if (! Storage::disk('local')->exists($dataPath)) {
            return [];
        }

        $data = json_decode(Storage::disk('local')->get($dataPath), true);
        if (! $data) {
            return [];
        }

        // Convert the associative array to a numeric array for Sushi
        $rows = [];
        foreach ($data as $packageName => $packageData) {
            // Ensure complex attributes are JSON strings before Sushi bulk insert
            // Sushi performs a raw insert and bypasses Eloquent casting, so arrays
            // must be encoded here to avoid "Array to string conversion" errors
            $jsonKeys = [
                'files',
                'markdown_files',
                'markdown_directory_tree',
                'logo',
                'dependencies',
                'maintainers',
                'downloads',
            ];

            foreach ($jsonKeys as $key) {
                if (array_key_exists($key, $packageData) && ! is_string($packageData[$key])) {
                    $packageData[$key] = json_encode($packageData[$key], JSON_UNESCAPED_SLASHES);
                }
            }

            // Normalize booleans to integers for SQLite consistency during bulk insert
            $boolKeys = ['direct-dependency', 'abandoned', 'has_newer_version'];
            foreach ($boolKeys as $key) {
                if (array_key_exists($key, $packageData) && is_bool($packageData[$key])) {
                    $packageData[$key] = $packageData[$key] ? 1 : 0;
                }
            }

            $packageData['id'] = count($rows) + 1; // Add an ID for Sushi
            $rows[] = $packageData;
        }

        return $rows;
    }

    /**
     * Get the schema for the model.
     *
     * @return array
     */
    public function getSchema()
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'version' => 'string',
            'type' => 'string',
            'usage_count' => 'integer',
            'files' => 'json',
            'markdown_files' => 'json',
            'markdown_directory_tree' => 'json',
            'logo' => 'json',
            'rank' => 'integer',
            'description' => 'string',
            'homepage' => 'string',
            'direct-dependency' => 'boolean',
            'source' => 'string',
            'abandoned' => 'boolean',
            'dependencies' => 'json',
            'repository' => 'string',
            'latest_version' => 'string',
            'has_newer_version' => 'boolean',
            'maintainers' => 'json',
            'downloads' => 'json',
        ];
    }

    /**
     * Refresh the package data by running the analysis command.
     *
     * @param  bool  $useQueue  Whether to use the queue for background processing
     */
    public static function refreshData(bool $useQueue = true): bool
    {
        try {
            if ($useQueue && config('queue.default') !== 'sync') {
                // Use the queue for background processing
                \App\Jobs\AnalyzeComposerPackagesJob::dispatch();

                return true;
            } else {
                // Run synchronously
                \Artisan::call('app:analyze-composer-packages');

                return true;
            }
        } catch (\Exception $e) {
            logger()->error('Failed to refresh composer package data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Get the top N most used packages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopPackages(int $limit = 10)
    {
        return static::orderBy('rank')->limit($limit)->get();
    }

    /**
     * Get packages with logos.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPackagesWithLogos(int $limit = 10, string $type = 'all', string $orderBy = 'rank', string $direction = 'asc')
    {
        $query = static::query()
            // Exclude null and JSON string "null" logos (Sushi encodes null as 'null')
            ->whereNotNull('logo')
            ->where('logo', '!=', 'null');

        // Filter by type if provided
        if (in_array($type, ['prod', 'dev'], true)) {
            $query->where('type', $type);
        }

        // Sanitize ordering
        $allowedOrder = ['rank', 'name', 'usage_count', 'type'];
        if (! in_array($orderBy, $allowedOrder, true)) {
            $orderBy = 'rank';
        }

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query->orderBy($orderBy, $direction)
            // Stable secondary sort for deterministic results
            ->orderBy('name', 'asc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get packages of a specific type (prod or dev).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPackagesByType(string $type)
    {
        return static::where('type', $type)->orderBy('rank')->get();
    }

    /**
     * Check if the package data needs to be refreshed by comparing checksums.
     */
    public static function needsRefresh(ComposerPackages $repository): bool
    {
        $checksumPath = 'database/data/composer-checksum.txt';
        if (! Storage::disk('local')->exists($checksumPath)) {
            return true;
        }

        $storedChecksum = Storage::disk('local')->get($checksumPath);
        $currentChecksum = $repository->getChecksum();

        return $storedChecksum !== $currentChecksum;
    }

    /**
     * Get all markdown files for a package.
     */
    public function getMarkdownFiles(): array
    {
        return $this->markdown_files ?? [];
    }

    /**
     * Get a specific markdown file by path.
     */
    public function getMarkdownFile(string $filePath): ?array
    {
        $files = $this->getMarkdownFiles();

        foreach ($files as $file) {
            if ($file['path'] === $filePath) {
                return $file;
            }
        }

        return null;
    }

    /**
     * Get the content of a specific markdown file.
     */
    public function getMarkdownContent(string $filePath): ?string
    {
        $file = $this->getMarkdownFile($filePath);

        if (! $file) {
            return null;
        }

        // If we have the content in the JSON, return it
        if (isset($file['content'])) {
            return $file['content'];
        }

        // If we have a storage_path, try to read it from the public storage disk
        if (isset($file['storage_path'])) {
            if (Storage::disk('public')->exists($file['storage_path'])) {
                return Storage::disk('public')->get($file['storage_path']);
            }
        }

        // Otherwise, try to read it from the file using the relative path
        if (isset($file['relative_path'])) {
            $relativePath = $file['relative_path'];
            if (Storage::disk('local')->exists($relativePath)) {
                return Storage::disk('local')->get($relativePath);
            }
        }

        return null;
    }

    /**
     * Get the PHPStorm URL for a markdown file.
     */
    public function getPhpStormUrl(string $filePath): ?string
    {
        $file = $this->getMarkdownFile($filePath);

        if (! $file) {
            return null;
        }

        return $file['url'] ?? null;
    }

    /**
     * Get the relative path for a markdown file.
     */
    public function getRelativePath(string $filePath): ?string
    {
        $file = $this->getMarkdownFile($filePath);

        if (! $file) {
            return null;
        }

        return $file['relative_path'] ?? null;
    }

    /**
     * Get the HTML content of a markdown file.
     */
    public function getMarkdownHtml(string $filePath): ?string
    {
        $file = $this->getMarkdownFile($filePath);

        if (! $file) {
            return null;
        }

        // If we have the HTML in the JSON, return it
        if (isset($file['html'])) {
            return $file['html'];
        }

        // Otherwise, get the content and convert it to HTML
        $content = $this->getMarkdownContent($filePath);
        if ($content) {
            return app(\Spatie\LaravelMarkdown\MarkdownRenderer::class)->toHtml($content);
        }

        return null;
    }

    /**
     * Find a package by name.
     */
    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get mock package data for testing.
     */
    public static function getMockPackageData(): array
    {
        // Create a simple mock data structure with a few packages
        return [
            [
                'id' => 1,
                'name' => 'laravel/framework',
                'version' => '10.0.0',
                'type' => 'prod',
                'usage_count' => 100,
                'rank' => 1,
                'logo' => [
                    'path' => null,
                    'url' => 'https://laravel.com/img/logomark.min.svg',
                ],
                'markdown_files' => [
                    [
                        'path' => 'readme.md',
                        'content' => '# Laravel Framework',
                        'html' => '<h1>Laravel Framework</h1>',
                    ],
                ],
                'description' => 'The Laravel Framework.',
                'homepage' => 'https://laravel.com',
                'direct-dependency' => true,
                'source' => 'https://github.com/laravel/framework',
                'abandoned' => false,
                'dependencies' => [],
                'markdown_directory_tree' => [
                    'files' => ['readme.md'],
                ],
            ],
            [
                'id' => 2,
                'name' => 'inertiajs/inertia-laravel',
                'version' => '1.0.0',
                'type' => 'prod',
                'usage_count' => 50,
                'rank' => 2,
                'logo' => [
                    'path' => null,
                    'url' => 'https://inertiajs.com/logo.svg',
                ],
                'markdown_files' => [
                    [
                        'path' => 'readme.md',
                        'content' => '# Inertia.js',
                        'html' => '<h1>Inertia.js</h1>',
                    ],
                ],
                'description' => 'The Inertia.js Laravel adapter.',
                'homepage' => 'https://inertiajs.com',
                'direct-dependency' => true,
                'source' => 'https://github.com/inertiajs/inertia-laravel',
                'abandoned' => false,
                'dependencies' => [],
                'markdown_directory_tree' => [
                    'files' => ['readme.md'],
                ],
            ],
        ];
    }
}
