<?php

use App\Models\User;

test('new user has free tier by default', function () {
    $user = User::factory()->create();

    expect($user->subscription_tier)->toBe('free');
    expect($user->upload_limit)->toBe(2);
    expect($user->doc_limit)->toBe(10);
});

test('pro tier has correct limits', function () {
    $user = User::factory()->create([
        'subscription_tier' => 'pro',
        'upload_limit' => 999,
        'doc_limit' => 100,
    ]);

    expect($user->subscription_tier)->toBe('pro');
    expect($user->upload_limit)->toBe(999);
    expect($user->doc_limit)->toBe(100);
});

test('lifetime tier has unlimited packages', function () {
    $user = User::factory()->create([
        'subscription_tier' => 'lifetime',
        'upload_limit' => 999,
        'doc_limit' => 999,
    ]);

    expect($user->subscription_tier)->toBe('lifetime');
    expect($user->doc_limit)->toBe(999);
});

test('user can have multiple packages', function () {
    $user = User::factory()->create();

    $user->packages()->create([
        'package_name' => 'laravel/framework',
        'version' => '^12.0',
        'type' => 'composer',
    ]);

    $user->packages()->create([
        'package_name' => 'spatie/laravel-data',
        'version' => '^4.0',
        'type' => 'composer',
    ]);

    expect($user->packages()->count())->toBe(2);
});

test('user packages have docs relationship', function () {
    $user = User::factory()->create();

    $package = $user->packages()->create([
        'package_name' => 'laravel/framework',
        'version' => '^12.0',
        'type' => 'composer',
    ]);

    $package->docs()->create([
        'user_id' => $user->id,
        'package_name' => 'laravel/framework',
        'file_path' => 'README.md',
        'content' => '# Laravel',
        'original_content' => '# Laravel',
        'upstream_hash' => 'abc123',
    ]);

    expect($package->docs()->count())->toBe(1);
});
