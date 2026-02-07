<?php

namespace App\Actions;

use App\DataTransferObjects\MarkdownToHtmlData;
use App\Services\PageService;

class ConvertMarkdownToHtmlAction
{
    public function __construct(
        private readonly PageService $pageService
    ) {}

    /**
     * Execute the action to convert markdown to HTML.
     *
     * @param string $markdown
     * @param string|null $disk
     * @return MarkdownToHtmlData
     */
    public function execute(string $markdown, ?string $disk = null): MarkdownToHtmlData
    {
        if ($disk) {
            $this->pageService->setDisk($disk);
        }

        $html = $this->pageService->convertToHtml($markdown);

        return new MarkdownToHtmlData(
            html: $html
        );
    }
}
