<?php

use App\Repositories\CachedPageRepository;
use App\Repositories\PageRepository;
use Illuminate\Support\Facades\Cache;

uses(Tests\TestCase::class);

test('getAllPages returns cached pages', function () {

    // Mock dependencies
    $pageRepository = Mockery::mock(PageRepository::class);

    // Set up expectations
    $pageRepository->shouldReceive('getAllPages')
        ->once()
        ->andReturn([
            ['filename' => 'page1.md', 'title' => 'Page 1'],
            ['filename' => 'page2.md', 'title' => 'Page 2']
        ]);

    // Mock Cache facade
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) {
            expect($key)->toBe('page_pages_all');
            expect($ttl)->toBe(86400);
            return true;
        })
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRepository($pageRepository);

    // Call the method
    $result = $repository->getAllPages();

    // Assert the result
    expect($result)->toBeArray()->toHaveCount(2);
    expect($result[0]['filename'])->toBe('page1.md');
    expect($result[1]['filename'])->toBe('page2.md');
});

test('getPageByFilename returns cached page', function () {

    // Mock dependencies
    $pageRepository = Mockery::mock(PageRepository::class);

    // Set up expectations
    $pageRepository->shouldReceive('sanitizeFilename')
        ->once()
        ->with('page1.md')
        ->andReturn('page1_md');

    $pageRepository->shouldReceive('getPageByFilename')
        ->once()
        ->with('page1.md')
        ->andReturn([
            'filename' => 'page1.md',
            'title' => 'Page 1',
            'markdown_content' => '# Page 1',
            'html_content' => '<h1>Page 1</h1>'
        ]);

    // Mock Cache facade
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) {
            expect($key)->toBe('page_pages_page1_md');
            expect($ttl)->toBe(86400);
            return true;
        })
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRepository($pageRepository);

    // Call the method
    $result = $repository->getPageByFilename('page1.md');

    // Assert the result
    expect($result)->toBeArray();
    expect($result['filename'])->toBe('page1.md');
    expect($result['title'])->toBe('Page 1');
});

test('createPage invalidates cache', function () {

    // Mock dependencies
    $pageRepository = Mockery::mock(PageRepository::class);

    // Set up expectations
    $pageRepository->shouldReceive('createPage')
        ->once()
        ->with('new-page.md', '# New Page', ['type' => 'doc', 'content' => []])
        ->andReturn([
            'filename' => 'new-page.md',
            'title' => 'New Page',
            'markdown_content' => '# New Page',
            'html_content' => '<h1>New Page</h1>'
        ]);

    $pageRepository->shouldReceive('sanitizeFilename')
        ->once()
        ->with('new-page.md')
        ->andReturn('new_page_md');

    // Mock Cache facade
    Cache::shouldReceive('forget')
        ->once()
        ->with('page_pages_new_page_md');

    Cache::shouldReceive('forget')
        ->once()
        ->with('page_pages_exists_new_page_md');

    Cache::shouldReceive('forget')
        ->once()
        ->with('page_pages_all');

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRepository($pageRepository);

    // Call the method
    $result = $repository->createPage('new-page.md', '# New Page', ['type' => 'doc', 'content' => []]);

    // Assert the result
    expect($result)->toBeArray();
    expect($result['filename'])->toBe('new-page.md');
    expect($result['title'])->toBe('New Page');
});

test('updatePage invalidates cache', function () {
    // Mock dependencies
    $pageRepository = Mockery::mock(PageRepository::class);

    // Set up expectations
    $pageRepository->shouldReceive('updatePage')
        ->once()
        ->with('page1.md', '# Updated Page', ['type' => 'doc', 'content' => []])
        ->andReturn([
            'filename' => 'page1.md',
            'title' => 'Updated Page',
            'markdown_content' => '# Updated Page',
            'html_content' => '<h1>Updated Page</h1>'
        ]);

    $pageRepository->shouldReceive('sanitizeFilename')
        ->once()
        ->with('page1.md')
        ->andReturn('page1_md');

    // Mock Cache facade
    Cache::shouldReceive('forget')
        ->once()
        ->with('page_pages_page1_md');

    Cache::shouldReceive('forget')
        ->once()
        ->with('page_pages_exists_page1_md');

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRepository($pageRepository);

    // Call the method
    $result = $repository->updatePage('page1.md', '# Updated Page', ['type' => 'doc', 'content' => []]);

    // Assert the result
    expect($result)->toBeArray();
    expect($result['filename'])->toBe('page1.md');
    expect($result['title'])->toBe('Updated Page');
});

test('deletePage invalidates cache', function () {

    // Mock dependencies
    $pageRepository = Mockery::mock(PageRepository::class);

    // Set up expectations
    $pageRepository->shouldReceive('deletePage')
        ->once()
        ->with('page1.md')
        ->andReturn(true);

    $pageRepository->shouldReceive('sanitizeFilename')
        ->once()
        ->with('page1.md')
        ->andReturn('page1_md');

    // Mock Cache facade
    Cache::shouldReceive('forget')
        ->once()
        ->with('page_pages_page1_md');

    Cache::shouldReceive('forget')
        ->once()
        ->with('page_pages_exists_page1_md');

    Cache::shouldReceive('forget')
        ->once()
        ->with('page_pages_all');

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRepository($pageRepository);

    // Call the method
    $result = $repository->deletePage('page1.md');

    // Assert the result
    expect($result)->toBeTrue();
});

test('pageExists returns cached result', function () {

    // Mock dependencies
    $pageRepository = Mockery::mock(PageRepository::class);

    // Set up expectations
    $pageRepository->shouldReceive('sanitizeFilename')
        ->once()
        ->with('page1.md')
        ->andReturn('page1_md');

    $pageRepository->shouldReceive('pageExists')
        ->once()
        ->with('page1.md')
        ->andReturn(true);

    // Mock Cache facade
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) {
            expect($key)->toBe('page_pages_exists_page1_md');
            expect($ttl)->toBe(86400);
            return true;
        })
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRepository($pageRepository);

    // Call the method
    $result = $repository->pageExists('page1.md');

    // Assert the result
    expect($result)->toBeTrue();
});

test('sanitizeFilename delegates to repository', function () {

    // Mock dependencies
    $pageRepository = Mockery::mock(PageRepository::class);

    // Set up expectations
    $pageRepository->shouldReceive('sanitizeFilename')
        ->once()
        ->with('page 1.md')
        ->andReturn('page_1_md');

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRepository($pageRepository);

    // Call the method
    $result = $repository->sanitizeFilename('page 1.md');

    // Assert the result
    expect($result)->toBe('page_1_md');
});
