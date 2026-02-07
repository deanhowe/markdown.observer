<?php

use App\Models\User;

test('homepage loads', function () {
    $response = $this->get('/');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Welcome', false));
});

test('pricing page loads', function () {
    $response = $this->get('/pricing');
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Pricing', false));
});

test('dashboard requires auth', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('upload requires auth', function () {
    $response = $this->get('/upload');
    $response->assertRedirect('/login');
});

test('authenticated user can access dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertOk();
});

test('authenticated user can access upload', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/upload');
    $response->assertOk();
});
