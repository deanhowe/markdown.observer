<?php

namespace App\Actions;

use App\DataTransferObjects\MarkdownFileData;
use App\Services\PackageMarkdownService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class GetMarkdownFileAction
{
    public function __construct(
        private readonly PackageMarkdownService $packageMarkdownService,
        private readonly MarkdownRenderer $markdownRenderer
    ) {}

    /**
     * Execute the action to get a specific markdown file.
     *
     * @param string $packageName
     * @param string $filePath
     * @return MarkdownFileData
     * @throws HttpResponseException
     */
    public function execute(string $packageName, string $filePath): MarkdownFileData
    {
        $package = $this->packageMarkdownService->getPackage($packageName);

        if (!$package) {
            abort(404, 'Package not found');
        }

        $content = $this->packageMarkdownService->getPackageMarkdownContent($packageName, $filePath);

        if (!$content) {
            abort(404, 'Markdown file not found');
        }

        $html = $this->markdownRenderer->toHtml($content);
        $phpStormUrl = $this->packageMarkdownService->getPhpStormUrl($packageName, $filePath);
        $relativePath = $this->packageMarkdownService->getRelativePath($packageName, $filePath);

        return new MarkdownFileData(
            package: $package,
            filePath: $filePath,
            content: $content,
            html: $html,
            phpStormUrl: $phpStormUrl,
            relativePath: $relativePath
        );
    }
}
