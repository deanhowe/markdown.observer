<?php

namespace App\Http\Controllers\Api;

use App\Facades\ComposerPackages;
use App\Http\Controllers\Controller;
use App\Services\ComposerPackagesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;

class DependencyController extends Controller
{
    public function __construct(
        private readonly ComposerPackagesService $composerPackagesService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        // Get pagination parameters
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $includeReadmeHtml = $request->boolean('include_readme_html', false);

        // Rate limit the analysis to prevent abuse
        $executed = RateLimiter::attempt(
            'composer-analysis',
            1, // attempts
            function() use ($page, $perPage, $includeReadmeHtml) {
                return $this->composerPackagesService->getPaginated($page, $perPage, $includeReadmeHtml);
            },
            60 // decay seconds
        );

        if (!$executed) {
            return response()->json([
                'error' => 'Too many requests. Please try again later.'
            ], 429);
        }

        return response()->json($executed);
    }

    public function direct(): JsonResponse
    {
        try {
            //$dependencies = $this->composerPackagesService->getDependencies(directOnly: true);
            $service = App::make(ComposerPackagesService::class);
            $dependencies = $service->getDependencies(directOnly: true);

            return response()->json([
                'success' => true,
                'data' => $dependencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cached(): JsonResponse
    {
        try {

            $data = ComposerPackages::cached();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'cached' => false,
                'message' => 'Caching is not yet implemented',
                'error' => 'Caching is not yet implemented'
            ], 420);
        }
    }
}
