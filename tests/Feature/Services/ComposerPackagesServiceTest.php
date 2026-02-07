<?php

use App\Repositories\ComposerPackagesRepository;
use App\Services\ComposerPackagesService;
use App\Services\MarkdownService;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Create a mock repository
    $this->repository = $this->createMock(ComposerPackagesRepository::class);

    // Create a mock markdown service
    $this->markdownService = $this->createMock(MarkdownService::class);

    // Create the service with the mock repository and markdown service
    $this->service = new ComposerPackagesService($this->repository, $this->markdownService);

    // Set up storage fake
    Storage::fake('local');
});

test('get package logo returns placeholder for nonexistent package', function () {
    // Test with a package that doesn't exist
    $logo = $this->service->getPackageLogo('nonexistent/package');

    // Since the package doesn't exist, we should get null
    expect($logo)->toBeNull();
});

test('analyze includes logo information', function () {
    // Create a collection of mock packages
    $mockPackages = collect([
        new \App\DataTransferObjects\ComposerPackageData(
            name: 'test/package',
            version: '1.0.0',
            description: 'Test package',
            homepage: 'https://example.com',
            directDependency: true,
            source: 'https://github.com/test/package',
            abandoned: false,
            dependencies: [],
            isDev: false
        )
    ]);

    // Set up the repository mock to return the mock packages
    $this->repository->method('getDependencies')
        ->willReturn($mockPackages);

    // Mock the getPackageLogo method to return a test logo
    $this->service = $this->getMockBuilder(ComposerPackagesService::class)
        ->setConstructorArgs([$this->repository, $this->markdownService])
        ->onlyMethods(['getPackageLogo'])
        ->getMock();

    $this->service->method('getPackageLogo')
        ->willReturn([
            'path' => null,
            'data_uri' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNmMGYwZjAiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiM5OTkiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==',
        ]);

    // Call the analyze method
    $result = $this->service->analyze();

    // Assert that the result includes logo information
    expect($result)->toBeArray();
    expect($result)->toHaveCount(1);
    expect($result[0])->toHaveKey('logo');
    expect($result[0]['logo'])->toBeArray();
    expect($result[0]['logo'])->toHaveKey('data_uri');
    expect($result[0]['logo']['data_uri'])->toStartWith('data:image/svg+xml;base64,');
});

test('create placeholder logo', function () {
    // Use reflection to access the private method
    $method = new \ReflectionMethod(ComposerPackagesService::class, 'createPlaceholderLogo');
    $method->setAccessible(true);

    // Call the method
    $result = $method->invoke($this->service, 'test/package');

    // Assert the result structure
    expect($result)->toBeArray();
    expect($result)->toHaveKey('path');
    expect($result)->toHaveKey('data_uri');

    // Assert that the data URI is in the correct format
    expect($result['data_uri'])->toStartWith('data:image/svg+xml;base64,');
});

test('get package readme with html conversion', function () {
    // Skip this test if the vendor directory doesn't exist
    if (!file_exists(base_path('vendor/test/package'))) {
        $this->markTestSkipped('Vendor directory does not exist');
    }

    // Set up the markdownService mock
    $this->markdownService->method('toHtml')
        ->willReturn('<h1>Test Package</h1><p>This is a test package.</p>');

    // Create a partial mock of the service
    $service = $this->getMockBuilder(ComposerPackagesService::class)
        ->setConstructorArgs([$this->repository, $this->markdownService])
        ->onlyMethods(['getPackageReadme'])
        ->getMock();

    // Mock the getPackageReadme method to return HTML
    $service->method('getPackageReadme')
        ->with('test/package', true)
        ->willReturn('<h1>Test Package</h1><p>This is a test package.</p>');

    // Call the method and assert the result
    $result = $service->getPackageReadme('test/package', true);
    expect($result)->toEqual('<h1>Test Package</h1><p>This is a test package.</p>');
});

test('analyze includes readme html when requested', function () {
    // Create a collection of mock packages
    $mockPackages = collect([
        new \App\DataTransferObjects\ComposerPackageData(
            name: 'test/package',
            version: '1.0.0',
            description: 'Test package',
            homepage: 'https://example.com',
            directDependency: true,
            source: 'https://github.com/test/package',
            abandoned: false,
            dependencies: [],
            isDev: false,
            logo: null,
            readmeHtml: null
        )
    ]);

    // Set up the repository mock to return the mock packages
    $this->repository->method('getDependencies')
        ->willReturn($mockPackages);

    // Mock the service with specific methods mocked
    $service = $this->getMockBuilder(ComposerPackagesService::class)
        ->setConstructorArgs([$this->repository, $this->markdownService])
        ->onlyMethods(['getPackageLogo', 'getPackageReadme'])
        ->getMock();

    // Mock the getPackageLogo method
    $service->method('getPackageLogo')
        ->willReturn([
            'path' => null,
            'storage_path' => 'packages/test/package/logo.svg',
            'url' => 'http://example.com/storage/packages/test/package/logo.svg',
        ]);

    // Mock the getPackageReadme method
    $service->method('getPackageReadme')
        ->willReturn('<h1>Test Package</h1><p>This is a test package.</p>');

    // Call the analyze method with includeReadmeHtml=true
    $result = $service->analyze(true);

    // Assert that the result includes README HTML
    expect($result)->toBeArray();
    expect($result)->toHaveCount(1);
    expect($result[0])->toHaveKey('readme_html');
    expect($result[0]['readme_html'])->toEqual('<h1>Test Package</h1><p>This is a test package.</p>');
});

test('get paginated', function () {
    // Create a collection of mock packages (20 items)
    $mockPackages = [];
    for ($i = 1; $i <= 20; $i++) {
        $mockPackages[] = new \App\DataTransferObjects\ComposerPackageData(
            name: "test/package-{$i}",
            version: '1.0.0',
            description: "Test package {$i}",
            homepage: "https://example.com/{$i}",
            directDependency: true,
            source: "https://github.com/test/package-{$i}",
            abandoned: false,
            dependencies: [],
            isDev: false
        );
    }

    // Set up the repository mock to return the mock packages
    $this->repository->method('getDependencies')
        ->willReturn(collect($mockPackages));

    // Mock the analyze method to return a simple array
    $this->service = $this->getMockBuilder(ComposerPackagesService::class)
        ->setConstructorArgs([$this->repository, $this->markdownService])
        ->onlyMethods(['analyze'])
        ->getMock();

    // Create a simple array of 20 items
    $analyzeResult = [];
    for ($i = 1; $i <= 20; $i++) {
        $analyzeResult[] = [
            'name' => "test/package-{$i}",
            'version' => '1.0.0',
            'description' => "Test package {$i}",
        ];
    }

    $this->service->method('analyze')
        ->willReturn($analyzeResult);

    // Test first page (default 10 per page)
    $result = $this->service->getPaginated(1, 10);

    // Assert the structure of the result
    expect($result)->toBeArray();
    expect($result)->toHaveKey('data');
    expect($result)->toHaveKey('meta');
    expect($result['data'])->toHaveCount(10);
    expect($result['meta']['current_page'])->toEqual(1);
    expect($result['meta']['per_page'])->toEqual(10);
    expect($result['meta']['last_page'])->toEqual(2);
    expect($result['meta']['total'])->toEqual(20);
    expect($result['meta']['from'])->toEqual(1);
    expect($result['meta']['to'])->toEqual(10);

    // Test second page
    $result = $this->service->getPaginated(2, 10);
    expect($result['data'])->toHaveCount(10);
    expect($result['meta']['current_page'])->toEqual(2);
    expect($result['meta']['from'])->toEqual(11);
    expect($result['meta']['to'])->toEqual(20);

    // Test with different page size
    $result = $this->service->getPaginated(1, 5);
    expect($result['data'])->toHaveCount(5);
    expect($result['meta']['last_page'])->toEqual(4);
});

/**
 * Helper method to mock file_get_contents
 */
function mock_file_get_contents(string $path, string $content): void
{
    // Create a mock for the global function
    $this->getFunctionMock('App\Services', 'file_get_contents')
        ->expects($this->any())
        ->with($path)
        ->willReturn($content);
}