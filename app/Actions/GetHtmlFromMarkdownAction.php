<?php

namespace App\Actions;

use App\Services\PackageMarkdownService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class GetHtmlFromMarkdownAction
{
    public function __construct(
        private readonly PackageMarkdownService $packageMarkdownService,
        private readonly \App\Services\MarkdownService $markdownService
    ) {}

    /**
     * Execute the action to get HTML from markdown content.
     *
     * @param string $packageName
     * @param string $filePath
     * @return string
     * @throws HttpResponseException
     */
    public function execute(string $packageName, string $filePath): string
    {
        $content = $this->packageMarkdownService->getPackageMarkdownContent($packageName, $filePath);

        if (!$content) {
            abort(404, 'Markdown file not found');
        }

        return $this->markdownService->toHtml($content);
    }
}
