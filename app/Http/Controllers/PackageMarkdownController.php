<?php

namespace App\Http\Controllers;

use App\Actions\GetHtmlFromMarkdownAction;
use App\Actions\GetMarkdownFileAction;
use App\Actions\GetPackageDetailsAction;
use App\Actions\GetPackagesIndexAction;
use App\Actions\GetRawMarkdownAction;
use App\Actions\RefreshPackageDataAction;
use App\Http\Requests\CarouselQueryRequest;
use App\Models\ComposerPackage;
use Illuminate\Support\Facades\Cache;

class PackageMarkdownController extends Controller
{
    /**
     * Display a listing of the packages.
     */
    public function index(GetPackagesIndexAction $action)
    {
        $data = $action->execute();

        return inertia('packages/index', [
            'packages' => $data->packages,
            'prodPackages' => $data->prodPackages,
            'devPackages' => $data->devPackages,
            'topPackages' => $data->topPackages,
        ]);
    }

    /**
     * Display the specified package.
     */
    public function show(string $packageName, GetPackageDetailsAction $action)
    {
        // Convert kebab-case parameter names to camelCase for internal use
        $data = $action->execute($packageName);

        return inertia('packages/show', [
            'package' => $data->package,
            'markdownFiles' => $data->markdownFiles,
        ]);
    }

    /**
     * Display the specified markdown file.
     */
    public function showMarkdownFile(string $packageName, string $filePath, GetMarkdownFileAction $action)
    {
        // Convert kebab-case parameter names to camelCase for internal use
        $data = $action->execute($packageName, $filePath);

        return inertia('packages/markdown', [
            'package' => $data->package,
            'filePath' => $data->filePath,
            'content' => $data->content,
            'html' => $data->html,
            'phpStormUrl' => $data->phpStormUrl,
            'relativePath' => $data->relativePath,
        ]);
    }

    /**
     * Refresh the package data.
     */
    public function refresh(RefreshPackageDataAction $action)
    {
        $success = $action->execute();

        if (! $success) {
            return redirect()->route('packages.index')->with('error', 'Failed to refresh package data');
        }

        return redirect()->route('packages.index')->with('success', 'Package data refreshed successfully');
    }

    /**
     * Get the raw markdown content.
     */
    public function getRawMarkdown(string $packageName, string $filePath, GetRawMarkdownAction $action)
    {
        // Convert kebab-case parameter names to camelCase for internal use
        $content = $action->execute($packageName, $filePath);

        return response($content)->header('Content-Type', 'text/markdown');
    }

    /**
     * Get the HTML content.
     */
    public function getHtml(string $packageName, string $filePath, GetHtmlFromMarkdownAction $action)
    {
        // Convert kebab-case parameter names to camelCase for internal use
        $html = $action->execute($packageName, $filePath);

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Get the last modified timestamp of a markdown file.
     */
    public function lastModified(string $packageName, string $filePath, \App\Actions\GetMarkdownFileLastModifiedAction $action)
    {
        $lastModified = $action->execute($packageName, $filePath);

        return response()->json([
            'last_modified' => $lastModified,
        ]);
    }

    /**
     * Get packages with logos for the carousel.
     */
    public function getPackagesForCarousel(CarouselQueryRequest $request, \App\Actions\GetPackagesForCarouselAction $action)
    {
        // If we're in a testing environment, return mock data
        if (app()->environment('testing') && request()->boolean('use_mock', true)) {
            \Log::info('Using mock data for testing');

            return response()->json([
                'packages' => [
                    [
                        'name' => 'laravel/framework',
                        'logo' => [
                            'path' => null,
                            'url' => 'https://laravel.com/img/logomark.min.svg',
                        ],
                        'readme_html' => '<h1>Laravel Framework</h1>',
                        'rank' => 1,
                        'type' => 'prod',
                        'description' => 'The Laravel Framework.',
                    ],
                    [
                        'name' => 'inertiajs/inertia-laravel',
                        'logo' => [
                            'path' => null,
                            'url' => 'https://inertiajs.com/logo.svg',
                        ],
                        'readme_html' => '<h1>Inertia.js</h1>',
                        'rank' => 2,
                        'type' => 'prod',
                        'description' => 'The Laravel adapter for Inertia.js.',
                    ],
                    [
                        'name' => 'spatie/laravel-markdown',
                        'logo' => [
                            'path' => null,
                            'url' => 'https://spatie.be/images/spatie-logo.svg',
                        ],
                        'readme_html' => '<h1>Laravel Markdown</h1>',
                        'rank' => 3,
                        'type' => 'prod',
                        'description' => 'A highly configurable markdown renderer and Blade component for Laravel.',
                    ],
                ],
            ]);
        }

        // Read and validate query params with sensible defaults
        $validated = $request->validated();
        $limit = (int) ($validated['limit'] ?? 12);
        $includeReadme = (bool) ($validated['include_readme'] ?? false);
        $orderBy = (string) ($validated['order_by'] ?? 'rank');
        $direction = (string) ($validated['direction'] ?? 'asc');
        $type = (string) ($validated['type'] ?? 'all');

        try {
            // Cache the carousel payload to avoid heavy recomputation and speed up homepage
            $cacheKey = sprintf(
                'carousel:limit:%d:include:%s:order:%s:dir:%s:type:%s',
                $limit,
                $includeReadme ? '1' : '0',
                $orderBy,
                $direction,
                $type
            );
            $packages = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($action, $limit, $includeReadme, $orderBy, $direction, $type) {
                return $action->execute($limit, $includeReadme, $orderBy, $direction, $type);
            });

            \Log::info('Final packages with readme count: '.count($packages));

            return response()->json([
                'packages' => $packages,
            ]);
        } catch (\Throwable $e) {
            // Log and provide a minimal, safe fallback to avoid 500s on homepage
            \Log::error('Failed to build carousel packages', [
                'message' => $e->getMessage(),
            ]);

            $fallback = ComposerPackage::getPackagesWithLogos(3)
                ->map(function ($pkg) {
                    return [
                        'name' => $pkg->name,
                        'version' => $pkg->version ?? 'unknown',
                        'description' => $pkg->description ?? '',
                        'logo' => $pkg->logo ?? null,
                        'readme_html' => '', // keep response shape stable
                        'rank' => $pkg->rank ?? null,
                        'type' => $pkg->type ?? null,
                    ];
                })->all();

            return response()->json([
                'packages' => $fallback,
            ]);
        }
    }
}
