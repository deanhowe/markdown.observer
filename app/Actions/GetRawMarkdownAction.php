<?php

namespace App\Actions;

use App\Services\PackageMarkdownService;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetRawMarkdownAction
{
    public function __construct(
        private readonly PackageMarkdownService $packageMarkdownService
    ) {}

    /**
     * Execute the action to get raw markdown content.
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

        return $content;
    }
}
