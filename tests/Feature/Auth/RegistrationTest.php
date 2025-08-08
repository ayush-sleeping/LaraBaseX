<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    // Seed roles and permissions for the test
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    $response = $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // User is created but not authenticated (INACTIVE status)
    $this->assertGuest();
    $response->assertRedirect(route('login'));

    // Verify user was created in database
    $user = \App\Models\User::where('email', 'test@example.com')->first();
    $this->assertDatabaseHas('users', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'status' => 'INACTIVE',
    ]);
    
    // Verify user has the "User" role assigned
    expect($user)->not->toBeNull();
    expect($user->hasRole('User'))->toBeTrue('User should have the "User" role assigned during registration');
});
