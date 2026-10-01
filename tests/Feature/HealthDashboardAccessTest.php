<?php

use App\Models\User;

// /health shows user counts, revenue and server versions. It used to be
// public and linked from the homepage footer.

test('guests are sent to log in', function () {
    $this->get('/health')->assertRedirect('/login');
});

test('signed-in non-admins are refused', function () {
    config(['app.admin_emails' => ['admin@example.com']]);

    $this->actingAs(User::factory()->create(['email' => 'someone@example.com']))
        ->get('/health')
        ->assertForbidden();
});

test('nobody gets in when no admin is configured', function () {
    config(['app.admin_emails' => []]);

    $this->actingAs(User::factory()->create())->get('/health')->assertForbidden();
});

test('admins can see it, whatever the email case', function () {
    config(['app.admin_emails' => ['admin@example.com']]);

    $this->actingAs(User::factory()->create(['email' => 'Admin@Example.com']))
        ->get('/health')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Health', false));
});
