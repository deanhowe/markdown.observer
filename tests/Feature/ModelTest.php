<?php

use App\Models\User;
use App\Models\UserPackage;
use App\Models\PackageDoc;

test('package has correct type', function () {
    $package = UserPackage::create([
        'user_id' => User::factory()->create()->id,
        'package_name' => 'laravel/framework',
        'version' => '^12.0',
        'type' => 'composer',
    ]);

    expect($package->type)->toBe('composer');
});

test('package stores version', function () {
    $package = UserPackage::create([
        'user_id' => User::factory()->create()->id,
        'package_name' => 'react',
        'version' => '^18.0.0',
        'type' => 'npm',
    ]);

    expect($package->version)->toBe('^18.0.0');
});

test('package tracks last sync time', function () {
    $package = UserPackage::create([
        'user_id' => User::factory()->create()->id,
        'package_name' => 'laravel/framework',
        'version' => '^12.0',
        'type' => 'composer',
        'last_synced_at' => now(),
    ]);

    expect($package->last_synced_at)->not->toBeNull();
});

test('doc belongs to user', function () {
    $user = User::factory()->create();

    $doc = PackageDoc::create([
        'user_id' => $user->id,
        'package_name' => 'laravel/framework',
        'file_path' => 'README.md',
        'content' => '# Laravel',
        'original_content' => '# Laravel',
        'upstream_hash' => 'abc123',
    ]);

    expect($doc->user_id)->toBe($user->id);
});

test('doc tracks edit status', function () {
    $doc = PackageDoc::create([
        'user_id' => User::factory()->create()->id,
        'package_name' => 'laravel/framework',
        'file_path' => 'README.md',
        'content' => '# Edited',
        'original_content' => '# Original',
        'upstream_hash' => 'abc123',
        'is_edited' => true,
    ]);

    expect($doc->is_edited)->toBeTrue();
    expect($doc->content)->not->toBe($doc->original_content);
});

test('doc stores upstream hash', function () {
    $doc = PackageDoc::create([
        'user_id' => User::factory()->create()->id,
        'package_name' => 'laravel/framework',
        'file_path' => 'README.md',
        'content' => '# Laravel',
        'original_content' => '# Laravel',
        'upstream_hash' => 'abc123def456',
    ]);

    expect($doc->upstream_hash)->toBe('abc123def456');
});

test('multiple docs can exist for same package', function () {
    $user = User::factory()->create();

    PackageDoc::create([
        'user_id' => $user->id,
        'package_name' => 'laravel/framework',
        'file_path' => 'README.md',
        'content' => '# Laravel',
        'original_content' => '# Laravel',
        'upstream_hash' => 'abc123',
    ]);

    PackageDoc::create([
        'user_id' => $user->id,
        'package_name' => 'laravel/framework',
        'file_path' => 'docs/installation.md',
        'content' => '# Installation',
        'original_content' => '# Installation',
        'upstream_hash' => 'def456',
    ]);

    $docs = PackageDoc::where('user_id', $user->id)
        ->where('package_name', 'laravel/framework')
        ->get();

    expect($docs->count())->toBe(2);
});
