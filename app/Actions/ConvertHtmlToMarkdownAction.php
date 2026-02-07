<?php

namespace App\Actions;

use App\DataTransferObjects\HtmlToMarkdownData;
use App\Services\PageService;

class ConvertHtmlToMarkdownAction
{
    public function __construct(
        private readonly PageService $pageService
    ) {}

    /**
     * Execute the action to convert HTML to markdown.
     *
     * @param string $html
     * @param string|null $disk
     * @return HtmlToMarkdownData
     */
    public function execute(string $html, ?string $disk = null): HtmlToMarkdownData
    {
        if ($disk) {
            $this->pageService->setDisk($disk);
        }

        $markdown = $this->pageService->convertToMarkdown($html);

        return new HtmlToMarkdownData(
            markdown: $markdown
        );
    }
}
