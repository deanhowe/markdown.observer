<?php

use App\Models\User;
use App\Models\UserPackage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use App\Jobs\FetchPackageDocsJob;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can upload real composer.json and parse packages', function () {
    $composerJson = file_get_contents(base_path('composer.json'));
    $file = UploadedFile::fake()->createWithContent('composer.json', $composerJson);

    $response = $this->post('/upload', ['file' => $file]);

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('SelectPackages')
        ->has('packages')
    );
});

test('can select and save packages from upload', function () {
    Queue::fake();

    $response = $this->post('/packages/confirm', [
        'packages' => [
            ['name' => 'laravel/framework', 'version' => '^12.0', 'type' => 'composer'],
            ['name' => 'spatie/laravel-data', 'version' => '^4.0', 'type' => 'composer'],
            ['name' => 'inertiajs/inertia-laravel', 'version' => '^2.0', 'type' => 'composer'],
        ],
    ]);

    $response->assertRedirect('/dashboard');
    
    // Verify packages saved
    expect(UserPackage::where('user_id', $this->user->id)->count())->toBe(3);
    
    // Verify jobs queued
    Queue::assertPushed(FetchPackageDocsJob::class, 3);
});

test('respects free tier package limit', function () {
    // Free tier: 10 packages max
    $packages = [];
    for ($i = 1; $i <= 11; $i++) {
        $packages[] = "vendor/package-{$i}";
    }

    $response = $this->post('/packages/confirm', [
        'packages' => $packages,
    ]);

    $response->assertSessionHasErrors();
    expect(UserPackage::where('user_id', $this->user->id)->count())->toBe(0);
});

test('can upload package.json and parse npm packages', function () {
    $packageJson = json_encode([
        'name' => 'test-app',
        'dependencies' => [
            'react' => '^18.0.0',
            'vue' => '^3.0.0',
            '@inertiajs/react' => '^1.0.0',
        ],
        'devDependencies' => [
            'vite' => '^5.0.0',
        ],
    ]);

    $file = UploadedFile::fake()->createWithContent('package.json', $packageJson);

    $response = $this->post('/upload', ['file' => $file]);

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('SelectPackages')
        ->has('packages', 3)
    );
});

test('filters out php and extensions from composer.json', function () {
    $composerJson = json_encode([
        'require' => [
            'php' => '^8.2',
            'ext-json' => '*',
            'ext-mbstring' => '*',
            'laravel/framework' => '^12.0',
            'spatie/laravel-data' => '^4.0',
        ],
    ]);

    $file = UploadedFile::fake()->createWithContent('composer.json', $composerJson);

    $response = $this->post('/upload', ['file' => $file]);

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('SelectPackages')
        ->has('packages', 2)
    );
});

test('validates file is required', function () {
    $response = $this->post('/upload', []);
    $response->assertSessionHasErrors('file');
});

test('validates file must be json', function () {
    $file = UploadedFile::fake()->create('test.txt', 100);
    
    $response = $this->post('/upload', ['file' => $file]);
    $response->assertSessionHasErrors('file');
});

test('validates file size limit', function () {
    $file = UploadedFile::fake()->create('composer.json', 3000); // 3MB
    
    $response = $this->post('/upload', ['file' => $file]);
    $response->assertSessionHasErrors('file');
});
