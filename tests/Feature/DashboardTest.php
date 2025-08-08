<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    // Seed roles first to ensure User role exists
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    
    $user = User::factory()->create();
    $user->assignRole('User'); // Assign the User role for dashboard access
    
    $this->actingAs($user);

    $this->get('/dashboard')->assertOk();
});