<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
|
| These routes handle user account settings including profile management,
| password updates, and appearance preferences. All routes require
| authentication and are prefixed with 'settings/'.
|
*/

// -------------------------------------------------------------------------------------------------------- ::
Route::middleware('auth')->group(function () {

    // Redirects /settings to /settings/profile as the default landing page
    Route::redirect('settings', '/settings/profile');

    // Display profile settings form (first_name, last_name, email)
    // Update profile information with validation
    // Delete user account (with password confirmation)
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Display password change form
    // Update password with rate limiting (6 attempts per minute)
    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1') // Prevent brute force attacks
        ->name('settings.password.update');

    // Display appearance/theme settings (future expansion)
    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance');

});
