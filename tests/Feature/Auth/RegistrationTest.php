<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {

    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    // New accounts land on pricing: deliberate since c51202f (2026-06-30).
    $response->assertRedirect(route('pricing', absolute: false));
});
