<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    // Seed roles first to ensure User role exists
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    $user = User::factory()->create(['status' => 'ACTIVE']);
    $user->assignRole('User'); // Assign the User role for dashboard access

    $this->actingAs($user);

    $this->get('/dashboard')->assertOk();
});

test('inactive users cannot access the dashboard', function () {
    // Seed roles first to ensure User role exists
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    $user = User::factory()->create(['status' => 'INACTIVE']);
    $user->assignRole('User'); // Even with role, inactive users should be blocked

    $this->actingAs($user);

    // Should be redirected to login with error message
    $this->get('/dashboard')->assertRedirect('/login');
});
