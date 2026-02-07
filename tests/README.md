# Testing Guidelines

## Fixtures

The `tests/Fixtures` directory contains files and data used for testing purposes. This directory is organized to mirror the structure of the application's storage directories.

### File System Testing

For testing file operations, Laravel provides the `Storage::fake()` method which creates a temporary in-memory disk for testing. This is the recommended approach for testing file operations as it ensures that tests don't interfere with actual files on disk.

#### Setting Up File System Tests

1. In your test setup, use `Storage::fake('disk_name')` to create a fake disk:

```php
// Create a fake disk for testing
Storage::fake('test_pages');
```

2. If your application code uses a specific disk (e.g., 'pages'), you need to ensure that the application is using the same disk that your tests are checking. You can do this by setting the disk in your repository:

```php
// Get the page repository and set the disk
$pageRepository = app(\App\Repositories\Interfaces\PageRepositoryInterface::class);
$pageRepository->setDisk('test_pages');
```

3. Create test files on the fake disk:

```php
// Create a test file
Storage::disk('test_pages')->put('test-file.md', '# Test Content');
```

4. In your assertions, check the fake disk:

```php
// Assert that the file exists
expect(Storage::disk('test_pages')->exists('test-file.md'))->toBeTrue();

// Assert the file content
expect(Storage::disk('test_pages')->get('test-file.md'))->toBe('# Test Content');
```

#### Example Test

```php
test('can create a file', function () {
    // Create a fake disk
    Storage::fake('test_pages');
    
    // Get the repository and set the disk
    $repository = app(\App\Repositories\Interfaces\PageRepositoryInterface::class);
    $repository->setDisk('test_pages');
    
    // Call the method that creates a file
    $repository->createPage('test-page', '# Test Page');
    
    // Assert that the file was created
    expect(Storage::disk('test_pages')->exists('test-page.md'))->toBeTrue();
    expect(Storage::disk('test_pages')->get('test-page.md'))->toBe('# Test Page');
});
```

### Important Notes

1. Always use `Storage::fake()` for testing file operations, not real files on disk.
2. Make sure your application code is using the same disk that your tests are checking.
3. Clean up after your tests by using Laravel's built-in test isolation or by explicitly deleting test files.
4. Don't rely on the existence of files from previous tests, as tests should be independent and isolated.
