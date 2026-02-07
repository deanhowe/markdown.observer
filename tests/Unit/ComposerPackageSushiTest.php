<?php

uses(Tests\TestCase::class);

use App\Models\ComposerPackage;
use Illuminate\Support\Facades\Storage;

it('loads composer packages from JSON and casts arrays correctly', function () {
    // Fake the local storage disk used by the model
    Storage::fake('local');

    // Minimal package dataset with array/complex fields
    $data = [
        'vendor/package' => [
            'name' => 'vendor/package',
            'version' => '1.0.0',
            'type' => 'prod',
            'usage_count' => 1,
            'files' => [
                ['path' => 'src/Example.php'],
            ],
            'markdown_files' => [
                [
                    'path' => 'README.md',
                    'html' => '<h1>Readme</h1>',
                ],
            ],
            'markdown_directory_tree' => [
                'name' => 'root',
                'children' => [],
            ],
            'logo' => [
                'path' => null,
                'url' => 'https://example.test/logo.svg',
            ],
            'rank' => 1,
            'description' => 'Test package',
            'homepage' => 'https://example.test',
            'direct-dependency' => true,
            'source' => 'https://github.com/vendor/package',
            'abandoned' => false,
            'dependencies' => [
                'php' => '^8.2',
            ],
        ],
    ];

    // Write the JSON file that Sushi reads
    Storage::disk('local')->put(
        'database/data/composer-details.json',
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    // Ensure a checksum file exists to avoid any external refresh triggers
    $checksum = md5_file(base_path('composer.json'));
    Storage::disk('local')->put('database/data/composer-checksum.txt', $checksum);

    // Act: trigger Sushi to load rows and ensure no exception is thrown
    $count = ComposerPackage::query()->count();
    expect($count)->toBe(1);

    $first = ComposerPackage::query()->first();
    expect($first)->not->toBeNull();
    expect($first->name)->toBe('vendor/package');

    // Arrays should be cast back to arrays from JSON
    expect($first->logo)->toBeArray();
    expect($first->markdown_files)->toBeArray();
    expect($first->markdown_directory_tree)->toBeArray();
    expect($first->dependencies)->toBeArray();

    // Boolean casting
    expect($first->getAttribute('direct-dependency'))->toBeTrue();
    expect($first->abandoned)->toBeFalse();
});
