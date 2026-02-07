<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use League\HTMLToMarkdown\HtmlConverter;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Stevebauman\Purify\Facades\Purify;

class MarkdownService
{
    protected $markdownRenderer;
    protected $htmlConverter;
    protected $cacheDuration;

    public function __construct(MarkdownRenderer $markdownRenderer, HtmlConverter $htmlConverter)
    {
        $this->markdownRenderer = $markdownRenderer;
        $this->htmlConverter = $htmlConverter;
        $this->cacheDuration = config('markdown.cache_duration', 3600);
    }

    /**
     * Convert Markdown to HTML
     *
     * @param string $markdown
     * @return string
     */
    public function toHtml(string $markdown): string
    {
        // Generate a cache key based on the markdown content
        $cacheKey = 'markdown_html_' . md5($markdown);

        // Use cache to avoid converting the same markdown multiple times
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($markdown) {
            // Convert markdown to HTML
            $html = $this->markdownRenderer->toHtml($markdown);

            // Purify HTML to prevent XSS attacks
            return Purify::clean($html);
        });
    }

    /**
     * Convert HTML to Markdown
     *
     * @param string $html
     * @return string
     */
    public function toMarkdown(string $html): string
    {
        // Generate a cache key based on the HTML content
        $cacheKey = 'html_markdown_' . md5($html);

        // Use cache to avoid converting the same HTML multiple times
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($html) {
            // Convert HTML to markdown
            return $this->htmlConverter->convert($html);
        });
    }

    /**
     * Clear the markdown and HTML cache
     *
     * @param string|null $markdown
     * @param string|null $html
     * @return void
     */
    public function clearCache(?string $markdown = null, ?string $html = null): void
    {
        if ($markdown) {
            // Clear cache for specific markdown content
            Cache::forget('markdown_html_' . md5($markdown));
        }

        if ($html) {
            // Clear cache for specific HTML content
            Cache::forget('html_markdown_' . md5($html));
        }

        if (!$markdown && !$html) {
            // Clear all markdown and HTML cache
            // This is a simple approach; in a production environment,
            // you might want to use cache tags or a more targeted approach
            Cache::flush();
        }
    }

    /**
     * Sanitize HTML
     *
     * @param string $html
     * @return string
     */
    public function sanitizeHtml(string $html): string
    {
        return Purify::clean($html);
    }
}
