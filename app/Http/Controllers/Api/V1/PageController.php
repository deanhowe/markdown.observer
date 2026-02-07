<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageCollection;
use App\Http\Resources\PageResource;
use App\Services\PageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PageController extends Controller
{
    private $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Set the disk to use for storage operations
     *
     * @param Request $request
     * @return void
     */
    private function setDiskFromRequest(Request $request): void
    {
        if ($request->has('disk')) {
            $disk = $request->input('disk');
            $this->pageService->setDisk($disk);
        }
    }

    /**
     * Display a listing of the pages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->setDiskFromRequest($request);
        $pages = $this->pageService->getAllPages();
        return new PageCollection($pages);
    }

    /**
     * Store a newly created page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'filename' => 'required|string|max:255',
            'content' => 'required|string',
            'tiptap_json' => 'nullable|array',
            'disk' => 'nullable|string',
        ]);

        $this->setDiskFromRequest($request);

        $filename = $request->input('filename');
        $markdownContent = $request->input('content');
        $tiptapJson = $request->input('tiptap_json');

        try {
            $page = $this->pageService->createPage($filename, $markdownContent, $tiptapJson);

            return (new PageResource($page))
                ->additional([
                    'message' => 'Page created successfully',
                ])
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'meta' => [
                    'api_version' => 'v1',
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $filename
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $filename)
    {
        $this->setDiskFromRequest($request);

        $page = $this->pageService->getPageByFilename($filename);

        if (!$page) {
            return response()->json([
                'message' => 'Page not found',
                'meta' => [
                    'api_version' => 'v1',
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        return new PageResource($page);
    }

    /**
     * Update the specified page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $filename
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $filename)
    {
        $request->validate([
            'content' => 'required|string',
            'tiptap_json' => 'nullable|array',
            'disk' => 'nullable|string',
        ]);

        $this->setDiskFromRequest($request);

        $markdownContent = $request->input('content');
        $tiptapJson = $request->input('tiptap_json');

        try {
            $page = $this->pageService->updatePage($filename, $markdownContent, $tiptapJson);

            return (new PageResource($page))
                ->additional([
                    'message' => 'Page updated successfully',
                ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'meta' => [
                    'api_version' => 'v1',
                ],
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $filename
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $filename)
    {
        $this->setDiskFromRequest($request);

        try {
            $this->pageService->deletePage($filename);

            return response()->json([
                'message' => 'Page deleted successfully',
                'meta' => [
                    'api_version' => 'v1',
                ],
            ]);
        } catch (\Exception $e) {
            if ($e->getMessage() === 'Page not found') {
                return response()->json([
                    'message' => 'Page not found',
                    'meta' => [
                        'api_version' => 'v1',
                    ],
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => $e->getMessage(),
                'meta' => [
                    'api_version' => 'v1',
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Convert Markdown to HTML
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actions\ConvertMarkdownToHtmlAction  $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertToHtml(Request $request, \App\Actions\ConvertMarkdownToHtmlAction $action)
    {
        $request->validate([
            'markdown' => 'required|string',
            'disk' => 'nullable|string',
        ]);

        $markdown = $request->input('markdown');
        $disk = $request->input('disk');

        $data = $action->execute($markdown, $disk);

        return response()->json([
            'html' => $data->html,
            'meta' => [
                'api_version' => 'v1',
            ],
        ]);
    }

    /**
     * Convert HTML to Markdown
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actions\ConvertHtmlToMarkdownAction  $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertToMarkdown(Request $request, \App\Actions\ConvertHtmlToMarkdownAction $action)
    {
        $request->validate([
            'html' => 'required|string',
            'disk' => 'nullable|string',
        ]);

        $html = $request->input('html');
        $disk = $request->input('disk');

        $data = $action->execute($html, $disk);

        return response()->json([
            'markdown' => $data->markdown,
            'meta' => [
                'api_version' => 'v1',
            ],
        ]);
    }

    /**
     * Get all available storage disks
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDisks()
    {
        $disks = [];

        // Add standard disks
        $disks[] = ['name' => 'pages', 'label' => 'Pages (Default)'];
        $disks[] = ['name' => 'composer-packages', 'label' => 'Composer Packages'];
        $disks[] = ['name' => 'local', 'label' => 'Private (Your Files)'];

        // These disks are not required for the current implementation
        // $disks[] = ['name' => 's3', 'label' => 'Amazon S3'];
        // $disks[] = ['name' => 'github', 'label' => 'GitHub'];

        // Get all composer package disks
        $allDisks = array_keys(config('filesystems.disks'));
        foreach ($allDisks as $disk) {
            if (strpos($disk, 'package-') === 0) {
                // Convert disk name to package name for display
                $packageName = str_replace('package-', '', $disk);
                $packageName = str_replace('_', '/', $packageName);
                $disks[] = ['name' => $disk, 'label' => $packageName];
            }
        }

        return response()->json([
            'data' => $disks,
            'meta' => [
                'api_version' => 'v1',
            ],
        ]);
    }
}
