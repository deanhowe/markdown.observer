<?php

use App\Repositories\CachedPageRevisionRepository;
use App\Repositories\PageRevisionRepository;
use App\Models\PageRevision;

uses(Tests\TestCase::class);

test('getLatestRevision returns cached revision', function () {
    // Mock dependencies
    $pageRevisionRepository = Mockery::mock(PageRevisionRepository::class);

    // Create a mock revision object
    $revision = new PageRevision();
    $revision->filename = 'page1.md';
    $revision->markdown_content = '# Page 1';
    $revision->html_content = '<h1>Page 1</h1>';
    $revision->tiptap_json = ['type' => 'doc', 'content' => []];
    $revision->revision_type = 'update';

    // Set up expectations
    $pageRevisionRepository->shouldReceive('getLatestRevision')
        ->once()
        ->with('page1.md')
        ->andReturn($revision);

    // Mock Cache facade
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) {
            expect($key)->toBe('page_revision_latest_page1_md');
            expect($ttl)->toBe(86400);
            return true;
        })
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRevisionRepository($pageRevisionRepository);

    // Call the method
    $result = $repository->getLatestRevision('page1.md');

    // Assert the result
    expect($result)->toBeInstanceOf(PageRevision::class);
    expect($result->filename)->toBe('page1.md');
    expect($result->markdown_content)->toBe('# Page 1');
});

test('createRevision invalidates cache', function () {
    // Mock dependencies
    $pageRevisionRepository = Mockery::mock(PageRevisionRepository::class);

    // Create a mock revision object
    $revision = new PageRevision();
    $revision->filename = 'page1.md';
    $revision->markdown_content = '# Page 1';
    $revision->html_content = '<h1>Page 1</h1>';
    $revision->tiptap_json = ['type' => 'doc', 'content' => []];
    $revision->revision_type = 'update';

    // Set up expectations
    $pageRevisionRepository->shouldReceive('createRevision')
        ->once()
        ->with('page1.md', '# Page 1', '<h1>Page 1</h1>', ['type' => 'doc', 'content' => []], 'update')
        ->andReturn($revision);

    // Mock Cache facade
    Cache::shouldReceive('forget')
        ->once()
        ->with('page_revision_latest_page1_md');

    Cache::shouldReceive('forget')
        ->once()
        ->with('page_revision_history_page1_md');

    Cache::shouldReceive('forget')
        ->once()
        ->with('page_revision_has_revisions_page1_md');

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRevisionRepository($pageRevisionRepository);

    // Call the method
    $result = $repository->createRevision(
        'page1.md',
        '# Page 1',
        '<h1>Page 1</h1>',
        ['type' => 'doc', 'content' => []],
        'update'
    );

    // Assert the result
    expect($result)->toBeInstanceOf(PageRevision::class);
    expect($result->filename)->toBe('page1.md');
    expect($result->markdown_content)->toBe('# Page 1');
});

test('getRevisionHistory returns cached history', function () {
    // Mock dependencies
    $pageRevisionRepository = Mockery::mock(PageRevisionRepository::class);

    // Create mock revision objects
    $revision1 = new PageRevision();
    $revision1->filename = 'page1.md';
    $revision1->markdown_content = '# Page 1';
    $revision1->html_content = '<h1>Page 1</h1>';
    $revision1->revision_type = 'create';

    $revision2 = new PageRevision();
    $revision2->filename = 'page1.md';
    $revision2->markdown_content = '# Page 1 Updated';
    $revision2->html_content = '<h1>Page 1 Updated</h1>';
    $revision2->revision_type = 'update';

    // Set up expectations
    $pageRevisionRepository->shouldReceive('getRevisionHistory')
        ->once()
        ->with('page1.md')
        ->andReturn([$revision1, $revision2]);

    // Mock Cache facade
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) {
            expect($key)->toBe('page_revision_history_page1_md');
            expect($ttl)->toBe(86400);
            return true;
        })
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRevisionRepository($pageRevisionRepository);

    // Call the method
    $result = $repository->getRevisionHistory('page1.md');

    // Assert the result
    expect($result)->toBeArray()->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(PageRevision::class);
    expect($result[0]->revision_type)->toBe('create');
    expect($result[1]->revision_type)->toBe('update');
});

test('hasRevisions returns cached result', function () {
    // Mock dependencies
    $pageRevisionRepository = Mockery::mock(PageRevisionRepository::class);

    // Set up expectations
    $pageRevisionRepository->shouldReceive('hasRevisions')
        ->once()
        ->with('page1.md')
        ->andReturn(true);

    // Mock Cache facade
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) {
            expect($key)->toBe('page_revision_has_revisions_page1_md');
            expect($ttl)->toBe(86400);
            return true;
        })
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    // Create repository instance with mocked dependencies
    $repository = new CachedPageRevisionRepository($pageRevisionRepository);

    // Call the method
    $result = $repository->hasRevisions('page1.md');

    // Assert the result
    expect($result)->toBeTrue();
});
