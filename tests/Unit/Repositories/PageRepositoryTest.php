<?php

use App\Repositories\PageRepository;
use App\Services\MarkdownService;
use App\Repositories\Interfaces\PageRevisionRepositoryInterface;

uses(Tests\TestCase::class);

test('getAllPages returns all markdown files', function () {
    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);
    $pageRevisionRepository = Mockery::mock(PageRevisionRepositoryInterface::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create test files
    Storage::disk('test_pages')->put('page1.md', '# Page 1');
    Storage::disk('test_pages')->put('page2.md', '# Page 2');
    Storage::disk('test_pages')->put('not-markdown.txt', 'Not a markdown file');

    // Set up expectations for markdownService
    $markdownService->shouldReceive('toHtml')
        ->with('# Page 1')
        ->andReturn('<h1>Page 1</h1>');
    $markdownService->shouldReceive('toHtml')
        ->with('# Page 2')
        ->andReturn('<h1>Page 2</h1>');

    // Mock app() for PageRevisionRepositoryInterface
    app()->instance(PageRevisionRepositoryInterface::class, $pageRevisionRepository);

    // Set up expectations for pageRevisionRepository
    $pageRevisionRepository->shouldReceive('getLatestRevision')
        ->with('page1')
        ->andReturnNull();
    $pageRevisionRepository->shouldReceive('getLatestRevision')
        ->with('page2')
        ->andReturnNull();

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->getAllPages();

    // Assert the result
    expect($result)->toBeArray()->toHaveCount(2);
    expect($result[0]['filename'])->toBe('page1');
    expect($result[1]['filename'])->toBe('page2');
});

test('getPageByFilename returns page when it exists', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);
    $pageRevisionRepository = Mockery::mock(PageRevisionRepositoryInterface::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create test files
    Storage::disk('test_pages')->put('page1.md', '# Page 1');

    // Mock app() for PageRevisionRepositoryInterface
    app()->instance(PageRevisionRepositoryInterface::class, $pageRevisionRepository);

    // Set up expectations
    $markdownService->shouldReceive('toHtml')
        ->once()
        ->with('# Page 1')
        ->andReturn('<h1>Page 1</h1>');

    // Mock PageRevisionRepository
    $pageRevisionRepository->shouldReceive('getLatestRevision')
        ->once()
        ->with('page1')
        ->andReturnNull();

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->getPageByFilename('page1');

    // Assert the result
    expect($result)->toBeArray();
    expect($result['filename'])->toBe('page1');
    expect($result['markdown_content'])->toBe('# Page 1');
    expect($result['html_content'])->toBe('<h1>Page 1</h1>');
    expect($result['has_external_changes'])->toBeFalse();
});

test('getPageByFilename returns null when page does not exist', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->getPageByFilename('nonexistent');

    // Assert the result
    expect($result)->toBeNull();
});

test('createPage creates a new page', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Set up expectations
    $markdownService->shouldReceive('toHtml')
        ->once()
        ->with('# New Page')
        ->andReturn('<h1>New Page</h1>');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->createPage('new_page', '# New Page', ['type' => 'doc', 'content' => []]);

    // Assert the result
    expect($result)->toBeArray();
    expect($result['filename'])->toBe('new_page');
    expect($result['markdown_content'])->toBe('# New Page');
    expect($result['html_content'])->toBe('<h1>New Page</h1>');
    expect($result['tiptap_json'])->toBe(['type' => 'doc', 'content' => []]);

    // Verify the file was created
    Storage::disk('test_pages')->assertExists('new_page.md');
    expect(Storage::disk('test_pages')->get('new_page.md'))->toBe('# New Page');
});

test('createPage throws exception when page already exists', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create an existing file
    Storage::disk('test_pages')->put('existing_page.md', '# Existing Page');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method and expect an exception
    expect(fn() => $repository->createPage('existing_page', '# Existing Page'))
        ->toThrow(\Exception::class, 'File already exists');
});

test('updatePage updates an existing page', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create an existing file
    Storage::disk('test_pages')->put('page1.md', '# Page 1');

    // Set up expectations
    $markdownService->shouldReceive('toHtml')
        ->once()
        ->with('# Updated Page')
        ->andReturn('<h1>Updated Page</h1>');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->updatePage('page1', '# Updated Page', ['type' => 'doc', 'content' => []]);

    // Assert the result
    expect($result)->toBeArray();
    expect($result['filename'])->toBe('page1');
    expect($result['markdown_content'])->toBe('# Updated Page');
    expect($result['html_content'])->toBe('<h1>Updated Page</h1>');
    expect($result['tiptap_json'])->toBe(['type' => 'doc', 'content' => []]);

    // Verify the file was updated
    Storage::disk('test_pages')->assertExists('page1.md');
    expect(Storage::disk('test_pages')->get('page1.md'))->toBe('# Updated Page');
});

test('updatePage with different content', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create an existing file with different content
    Storage::disk('test_pages')->put('page1.md', '# Different Content');

    // Set up expectations
    $markdownService->shouldReceive('toHtml')
        ->once()
        ->with('# Updated Page')
        ->andReturn('<h1>Updated Page</h1>');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->updatePage('page1', '# Updated Page', ['type' => 'doc', 'content' => []]);

    // Assert the result
    expect($result)->toBeArray();
    expect($result['filename'])->toBe('page1');
    expect($result['markdown_content'])->toBe('# Updated Page');
    expect($result['html_content'])->toBe('<h1>Updated Page</h1>');
    expect($result['tiptap_json'])->toBe(['type' => 'doc', 'content' => []]);

    // Verify the file was updated
    Storage::disk('test_pages')->assertExists('page1.md');
    expect(Storage::disk('test_pages')->get('page1.md'))->toBe('# Updated Page');
});

test('updatePage throws exception when page does not exist', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method and expect an exception
    expect(fn() => $repository->updatePage('nonexistent', '# Nonexistent Page'))
        ->toThrow(\Exception::class, 'Page not found');
});

test('deletePage deletes an existing page', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create an existing file
    Storage::disk('test_pages')->put('page1.md', '# Page 1');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->deletePage('page1');

    // Assert the result
    expect($result)->toBeTrue();

    // Verify the file was deleted
    Storage::disk('test_pages')->assertMissing('page1.md');
});

test('deletePage returns false when page does not exist', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->deletePage('nonexistent');

    // Assert the result
    expect($result)->toBeFalse();
});

test('pageExists returns true when page exists', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create an existing file
    Storage::disk('test_pages')->put('page1.md', '# Page 1');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->pageExists('page1');

    // Assert the result
    expect($result)->toBeTrue();
});

test('pageExists returns false when page does not exist', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Use the test_pages disk
    Storage::fake('test_pages');

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Set the test disk
    $repository->setDisk('test_pages');

    // Call the method
    $result = $repository->pageExists('nonexistent');

    // Assert the result
    expect($result)->toBeFalse();
});

test('sanitizeFilename sanitizes filenames correctly', function () {

    // Mock dependencies
    $markdownService = Mockery::mock(MarkdownService::class);

    // Create repository instance with mocked dependencies
    $repository = new PageRepository($markdownService);

    // Test various filenames
    expect($repository->sanitizeFilename('page1'))->toBe('page1');
    expect($repository->sanitizeFilename('page 1'))->toBe('page-1');
    expect($repository->sanitizeFilename('page/1'))->toBe('1'); // basename('page/1') returns '1'
    expect($repository->sanitizeFilename('page.1'))->toBe('page-1');
    expect($repository->sanitizeFilename('page-1'))->toBe('page-1');
    expect($repository->sanitizeFilename('page_1'))->toBe('page_1');
});
