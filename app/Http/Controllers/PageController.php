<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class PageController extends Controller
{
    private $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Display a listing of the pages.
     */
    public function index()
    {
        $packages = \App\Models\UserPackage::where('user_id', auth()->id())
            ->with('docs')
            ->get();

        return \Inertia::render('Dashboard', ['packages' => $packages]);
    }

    /**
     * Show the form for editing a page.
     */
    public function edit(string $slug)
    {
        $path = resource_path("pages/{$slug}.md");
        
        if (!\File::exists($path)) {
            abort(404);
        }

        return \Inertia::render('PageEditor', [
            'page' => [
                'slug' => $slug,
                'content' => \File::get($path),
            ],
        ]);
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $path = resource_path("pages/{$validated['slug']}.md");
        \File::put($path, $validated['content']);

        return redirect()->route('home')->with('success', 'Page created!');
    }

    /**
     * Display the specified page.
     */
    public function show($filename)
    {
        $page = $this->pageService->getPageByFilename($filename);

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json([
            'filename' => $page['filename'],
            'markdown_content' => $page['markdown_content'],
            'html_content' => $page['html_content'],
            'tiptap_json' => $page['tiptap_json'],
            'last_modified' => $page['last_modified'],
        ]);
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, $filename)
    {
        $request->validate([
            'content' => 'required|string',
            'tiptap_json' => 'nullable|array',
        ]);

        $markdownContent = $request->input('content');
        $tiptapJson = $request->input('tiptap_json');

        try {
            $page = $this->pageService->updatePage($filename, $markdownContent, $tiptapJson);

            return response()->json([
                'message' => 'Page updated successfully',
                'filename' => $page['filename'],
                'markdown_content' => $markdownContent,
                'html_content' => $page['html_content'],
                'tiptap_json' => $tiptapJson,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Remove the specified page.
     */
    public function destroy($filename)
    {
        try {
            $this->pageService->deletePage($filename);

            return response()->json([
                'message' => 'Page deleted successfully',
            ]);
        } catch (\Exception $e) {
            if ($e->getMessage() === 'Page not found') {
                return response()->json(['message' => 'Page not found'], 404);
            }

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Convert Markdown to HTML
     */
    public function convertToHtml(Request $request)
    {
        $request->validate([
            'markdown' => 'required|string',
        ]);

        $markdown = $request->input('markdown');
        $html = $this->pageService->convertToHtml($markdown);

        return response()->json([
            'html' => $html,
        ]);
    }

    /**
     * Convert HTML to Markdown
     */
    public function convertToMarkdown(Request $request)
    {
        $request->validate([
            'html' => 'required|string',
        ]);

        $html = $request->input('html');
        $markdown = $this->pageService->convertToMarkdown($html);

        return response()->json([
            'markdown' => $markdown,
        ]);
    }
}
