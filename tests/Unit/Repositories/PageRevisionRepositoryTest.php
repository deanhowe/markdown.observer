<?php

use App\Repositories\PageRevisionRepository;
use App\Models\PageRevision;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('getLatestRevision returns latest revision', function () {
    // Create test data
    $olderRevision = PageRevision::factory()->create([
        'filename' => 'test-page.md',
        'markdown_content' => '# Older Version',
        'html_content' => '<h1>Older Version</h1>',
        'revision_type' => 'create',
        'created_at' => now()->subHour(),
    ]);

    $newerRevision = PageRevision::factory()->create([
        'filename' => 'test-page.md',
        'markdown_content' => '# Newer Version',
        'html_content' => '<h1>Newer Version</h1>',
        'revision_type' => 'update',
        'created_at' => now(),
    ]);

    // Create repository instance
    $repository = new PageRevisionRepository();

    // Call the method
    $result = $repository->getLatestRevision('test-page.md');

    // Assert the result
    expect($result)->toBeInstanceOf(PageRevision::class);
    expect($result->id)->toBe($newerRevision->id);
    expect($result->markdown_content)->toBe('# Newer Version');
});

test('getLatestRevision returns null when no revisions exist', function () {
    // Create repository instance
    $repository = new PageRevisionRepository();

    // Call the method
    $result = $repository->getLatestRevision('nonexistent.md');

    // Assert the result
    expect($result)->toBeNull();
});

test('createRevision creates a new revision', function () {
    // Create repository instance
    $repository = new PageRevisionRepository();

    // Call the method
    $result = $repository->createRevision(
        'new-page.md',
        '# New Page',
        '<h1>New Page</h1>',
        ['type' => 'doc', 'content' => []],
        'create'
    );

    // Assert the result
    expect($result)->toBeInstanceOf(PageRevision::class);
    expect($result->filename)->toBe('new-page.md');
    expect($result->markdown_content)->toBe('# New Page');
    expect($result->html_content)->toBe('<h1>New Page</h1>');
    expect($result->tiptap_json)->toBe(['type' => 'doc', 'content' => []]);
    expect($result->revision_type)->toBe('create');

    // Verify it was saved to the database
    $this->assertDatabaseHas('page_revisions', [
        'filename' => 'new-page.md',
        'markdown_content' => '# New Page',
        'html_content' => '<h1>New Page</h1>',
        'revision_type' => 'create',
    ]);
});

test('getRevisionHistory returns all revisions for a file', function () {
    // Create test data
    $revision1 = PageRevision::factory()->create([
        'filename' => 'test-page.md',
        'markdown_content' => '# Version 1',
        'html_content' => '<h1>Version 1</h1>',
        'revision_type' => 'create',
        'created_at' => now()->subHours(2),
    ]);

    $revision2 = PageRevision::factory()->create([
        'filename' => 'test-page.md',
        'markdown_content' => '# Version 2',
        'html_content' => '<h1>Version 2</h1>',
        'revision_type' => 'update',
        'created_at' => now()->subHour(),
    ]);

    $revision3 = PageRevision::factory()->create([
        'filename' => 'test-page.md',
        'markdown_content' => '# Version 3',
        'html_content' => '<h1>Version 3</h1>',
        'revision_type' => 'update',
        'created_at' => now(),
    ]);

    // Create a revision for a different file
    PageRevision::factory()->create([
        'filename' => 'other-page.md',
        'markdown_content' => '# Other Page',
        'html_content' => '<h1>Other Page</h1>',
        'revision_type' => 'create',
    ]);

    // Create repository instance
    $repository = new PageRevisionRepository();

    // Call the method
    $result = $repository->getRevisionHistory('test-page.md');

    // Assert the result
    expect($result)->toBeArray()->toHaveCount(3);
    expect($result[0]['id'])->toBe($revision3->id);
    expect($result[1]['id'])->toBe($revision2->id);
    expect($result[2]['id'])->toBe($revision1->id);
});

test('getRevisionHistory returns empty array when no revisions exist', function () {
    // Create repository instance
    $repository = new PageRevisionRepository();

    // Call the method
    $result = $repository->getRevisionHistory('nonexistent.md');

    // Assert the result
    expect($result)->toBeArray()->toBeEmpty();
});

test('hasRevisions returns true when revisions exist', function () {
    // Create test data
    PageRevision::factory()->create([
        'filename' => 'test-page.md',
        'markdown_content' => '# Test Page',
        'html_content' => '<h1>Test Page</h1>',
        'revision_type' => 'create',
    ]);

    // Create repository instance
    $repository = new PageRevisionRepository();

    // Call the method
    $result = $repository->hasRevisions('test-page.md');

    // Assert the result
    expect($result)->toBeTrue();
});

test('hasRevisions returns false when no revisions exist', function () {
    // Create repository instance
    $repository = new PageRevisionRepository();

    // Call the method
    $result = $repository->hasRevisions('nonexistent.md');

    // Assert the result
    expect($result)->toBeFalse();
});
