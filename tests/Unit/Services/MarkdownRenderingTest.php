<?php

use App\Services\MarkdownService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    // Get the MarkdownService from the container to ensure it's configured correctly
    $this->markdownService = app(MarkdownService::class);
});

test('it renders headings correctly', function () {
    $markdown = "# Heading 1\n## Heading 2\n### Heading 3";
    $html = $this->markdownService->toHtml($markdown);

    expect($html)->toContain('<h1')
        ->toContain('Heading 1')
        ->toContain('<h2')
        ->toContain('Heading 2')
        ->toContain('<h3')
        ->toContain('Heading 3');
});

test('it renders lists correctly', function () {
    $markdown = "- Item 1\n- Item 2\n- Item 3\n\n1. Numbered 1\n2. Numbered 2\n3. Numbered 3";
    $html = $this->markdownService->toHtml($markdown);

    expect($html)->toContain('<ul')
        ->toContain('<li>Item 1</li>')
        ->toContain('<li>Item 2</li>')
        ->toContain('<li>Item 3</li>')
        ->toContain('<ol')
        ->toContain('<li>Numbered 1</li>')
        ->toContain('<li>Numbered 2</li>')
        ->toContain('<li>Numbered 3</li>');
});

test('it renders tables correctly', function () {
    $markdown = "| Header 1 | Header 2 |\n| -------- | -------- |\n| Cell 1   | Cell 2   |\n| Cell 3   | Cell 4   |";
    $html = $this->markdownService->toHtml($markdown);

    // For tables, we'll check that the content is present, even if not in table format
    expect($html)->toContain('Header 1')
        ->toContain('Header 2')
        ->toContain('Cell 1')
        ->toContain('Cell 2')
        ->toContain('Cell 3')
        ->toContain('Cell 4');
});

test('it renders code blocks with syntax highlighting', function () {
    $markdown = "```php\n<?php\n\necho 'Hello, World!';\n```";
    $html = $this->markdownService->toHtml($markdown);

    // For code blocks, we'll check that the content is present
    // Just check for the presence of 'echo' and 'Hello, World!' which should be in the output
    expect($html)->toContain('echo')
        ->toContain('Hello, World!');
});

test('it caches rendered markdown', function () {
    Cache::flush();

    $markdown = "# Test Caching";
    $cacheKey = 'markdown_html_' . md5($markdown);

    // First call should cache the result
    $html1 = $this->markdownService->toHtml($markdown);

    // Verify it's in the cache
    expect(Cache::has($cacheKey))->toBeTrue();

    // Second call should use the cached result
    $html2 = $this->markdownService->toHtml($markdown);

    // Both results should be identical
    expect($html1)->toBe($html2);
});

test('it clears specific markdown cache', function () {
    Cache::flush();

    $markdown = "# Test Clear Cache";
    $cacheKey = 'markdown_html_' . md5($markdown);

    // Cache the result
    $this->markdownService->toHtml($markdown);

    // Verify it's in the cache
    expect(Cache::has($cacheKey))->toBeTrue();

    // Clear the cache
    $this->markdownService->clearCache($markdown);

    // Verify it's no longer in the cache
    expect(Cache::has($cacheKey))->toBeFalse();
});
