<?php

uses(Tests\TestCase::class);

use App\Actions\GetPackagesForCarouselAction;
use App\Contracts\ComposerPackages;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Mockery as m;

it('returns all packages with logos including rank and type, preserving README html', function () {
    Storage::fake('local');

    // Prepare two packages with logos and README.md
    $data = [
        'vendor/alpha' => [
            'name' => 'vendor/alpha',
            'version' => '1.0.0',
            'type' => 'prod',
            'usage_count' => 5,
            'files' => [],
            'markdown_files' => [
                ['path' => 'README.md', 'html' => '<h1>Alpha</h1>'],
            ],
            'logo' => ['path' => null, 'url' => 'https://example.test/alpha.svg'],
            'rank' => 1,
            'description' => 'Alpha desc',
            'homepage' => null,
            'direct-dependency' => true,
            'source' => null,
            'abandoned' => false,
            'dependencies' => [],
        ],
        'vendor/beta' => [
            'name' => 'vendor/beta',
            'version' => '2.0.0',
            'type' => 'dev',
            'usage_count' => 3,
            'files' => [],
            'markdown_files' => [
                ['path' => 'README.md', 'html' => '<h1>Beta</h1>'],
            ],
            'logo' => ['path' => null, 'url' => 'https://example.test/beta.svg'],
            'rank' => 2,
            'description' => 'Beta desc',
            'homepage' => null,
            'direct-dependency' => false,
            'source' => null,
            'abandoned' => false,
            'dependencies' => [],
        ],
    ];

    Storage::disk('local')->put(
        'database/data/composer-details.json',
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    // Ensure checksum matches mocked repository value so no refresh occurs
    Storage::disk('local')->put('database/data/composer-checksum.txt', 'checksum123');

    // Bind a mocked ComposerPackages repository
    App::bind(ComposerPackages::class, function () {
        $mock = m::mock(ComposerPackages::class);
        $mock->shouldReceive('getChecksum')->andReturn('checksum123');
        $mock->shouldReceive('getDependencies')->andReturn(collect());
        $mock->shouldReceive('getRequireString')->andReturn('');

        return $mock;
    });

    $action = app(GetPackagesForCarouselAction::class);
    $result = $action->execute(null); // null => all packages

    expect($result)->toBeArray()->and(count($result))->toBe(2);

    // Order should be by rank ascending
    expect($result[0]['name'])->toBe('vendor/alpha');
    expect($result[0]['rank'])->toBe(1);
    expect($result[0]['type'])->toBe('prod');
    expect($result[0]['readme_html'])->toContain('Alpha');

    expect($result[1]['name'])->toBe('vendor/beta');
    expect($result[1]['rank'])->toBe(2);
    expect($result[1]['type'])->toBe('dev');
    expect($result[1]['readme_html'])->toContain('Beta');
});

it('orders by name asc and desc deterministically', function () {
    Storage::fake('local');

    $data = [
        'vendor/alpha' => [
            'name' => 'vendor/alpha',
            'version' => '1.0.0',
            'type' => 'prod',
            'usage_count' => 5,
            'files' => [],
            'markdown_files' => [ ['path' => 'README.md', 'html' => '<h1>Alpha</h1>'] ],
            'logo' => ['path' => null, 'url' => 'https://example.test/alpha.svg'],
            'rank' => 2,
            'description' => 'Alpha desc',
        ],
        'vendor/zeta' => [
            'name' => 'vendor/zeta',
            'version' => '3.0.0',
            'type' => 'prod',
            'usage_count' => 7,
            'files' => [],
            'markdown_files' => [ ['path' => 'README.md', 'html' => '<h1>Zeta</h1>'] ],
            'logo' => ['path' => null, 'url' => 'https://example.test/zeta.svg'],
            'rank' => 3,
            'description' => 'Zeta desc',
        ],
        'vendor/beta' => [
            'name' => 'vendor/beta',
            'version' => '2.0.0',
            'type' => 'dev',
            'usage_count' => 10,
            'files' => [],
            'markdown_files' => [ ['path' => 'README.md', 'html' => '<h1>Beta</h1>'] ],
            'logo' => ['path' => null, 'url' => 'https://example.test/beta.svg'],
            'rank' => 1,
            'description' => 'Beta desc',
        ],
    ];

    Storage::disk('local')->put('database/data/composer-details.json', json_encode($data));
    Storage::disk('local')->put('database/data/composer-checksum.txt', 'checksum123');

    App::bind(ComposerPackages::class, function () {
        $mock = m::mock(ComposerPackages::class);
        $mock->shouldReceive('getChecksum')->andReturn('checksum123');
        $mock->shouldReceive('getDependencies')->andReturn(collect());
        $mock->shouldReceive('getRequireString')->andReturn('');
        return $mock;
    });

    $action = app(GetPackagesForCarouselAction::class);
    $asc = $action->execute(null, true, 'name', 'asc', 'all');
    $desc = $action->execute(null, true, 'name', 'desc', 'all');

    expect($asc[0]['name'])->toBe('vendor/alpha');
    expect($desc[0]['name'])->toBe('vendor/zeta');
});

it('filters by type and respects limit with includeReadme off keeping schema', function () {
    Storage::fake('local');

    $data = [
        'vendor/alpha' => [
            'name' => 'vendor/alpha',
            'version' => '1.0.0',
            'type' => 'prod',
            'usage_count' => 5,
            'files' => [],
            'markdown_files' => [ ['path' => 'README.md', 'html' => '<h1>Alpha</h1>'] ],
            'logo' => ['path' => null, 'url' => 'https://example.test/alpha.svg'],
            'rank' => 2,
        ],
        'vendor/beta' => [
            'name' => 'vendor/beta',
            'version' => '2.0.0',
            'type' => 'dev',
            'usage_count' => 10,
            'files' => [],
            'markdown_files' => [ ['path' => 'README.md', 'html' => '<h1>Beta</h1>'] ],
            'logo' => ['path' => null, 'url' => 'https://example.test/beta.svg'],
            'rank' => 1,
        ],
    ];

    Storage::disk('local')->put('database/data/composer-details.json', json_encode($data));
    Storage::disk('local')->put('database/data/composer-checksum.txt', 'checksum123');

    App::bind(ComposerPackages::class, function () {
        $mock = m::mock(ComposerPackages::class);
        $mock->shouldReceive('getChecksum')->andReturn('checksum123');
        $mock->shouldReceive('getDependencies')->andReturn(collect());
        $mock->shouldReceive('getRequireString')->andReturn('');
        return $mock;
    });

    $action = app(GetPackagesForCarouselAction::class);
    $devOnly = $action->execute(null, false, 'rank', 'asc', 'dev');
    expect($devOnly)->toBeArray()->and(count($devOnly))->toBe(1);
    expect($devOnly[0]['type'])->toBe('dev');
    expect($devOnly[0])->toHaveKey('readme_html');
    expect($devOnly[0]['readme_html'])->toBe('');

    $limited = $action->execute(1, false, 'rank', 'asc', 'all');
    expect(count($limited))->toBe(1);
});

it('excludes packages without a logo', function () {
    Storage::fake('local');

    $data = [
        'vendor/withlogo' => [
            'name' => 'vendor/withlogo',
            'version' => '1.0.0',
            'type' => 'prod',
            'usage_count' => 1,
            'files' => [],
            'markdown_files' => [ ['path' => 'README.md', 'html' => '<h1>Readme</h1>'] ],
            'logo' => ['path' => null, 'url' => 'https://example.test/logo.svg'],
            'rank' => 1,
        ],
        'vendor/nologo' => [
            'name' => 'vendor/nologo',
            'version' => '1.0.0',
            'type' => 'prod',
            'usage_count' => 2,
            'files' => [],
            'markdown_files' => [ ['path' => 'README.md', 'html' => '<h1>No Logo</h1>'] ],
            // explicit null logo to keep consistent columns and simulate missing logo
            'logo' => null,
            'rank' => 2,
        ],
    ];

    Storage::disk('local')->put('database/data/composer-details.json', json_encode($data));
    Storage::disk('local')->put('database/data/composer-checksum.txt', 'checksum123');

    App::bind(ComposerPackages::class, function () {
        $mock = m::mock(ComposerPackages::class);
        $mock->shouldReceive('getChecksum')->andReturn('checksum123');
        $mock->shouldReceive('getDependencies')->andReturn(collect());
        $mock->shouldReceive('getRequireString')->andReturn('');
        return $mock;
    });

    $action = app(GetPackagesForCarouselAction::class);
    $result = $action->execute(null, true, 'rank', 'asc', 'all');
    expect(count($result))->toBe(1);
    expect($result[0]['name'])->toBe('vendor/withlogo');
});
