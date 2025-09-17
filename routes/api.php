<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes are loaded by the RouteServiceProvider within the "api"
| middleware group. Organize your API for clarity and scalability.
|
*/

// Public API routes (with basic authentication)
// -------------------------------------------------------------------------------------------------------- ::
Route::middleware(['basic_auth'])->group(function () {
    // App version check
    Route::post('get-app-version', [AuthController::class, 'getAppVersion'])->name('api.get-app-version');

    // Authentication
    Route::post('register', [AuthController::class, 'register'])->name('api.register');
    Route::post('verify-otp', [AuthController::class, 'verifyOTP'])->name('api.verify-otp');
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
    Route::post('resend-otp', [AuthController::class, 'resendOTP'])->name('api.resend-otp');

    // Protected API routes (require token authentication)
    Route::middleware(['token'])->group(function () {
        // User endpoints
        Route::post('user', [AuthController::class, 'getUser'])->name('api.user');
        Route::post('user/update', [AuthController::class, 'updateUser'])->name('api.user.update');

        // Sliders
        Route::post('sliders', [HomeController::class, 'getSliders'])->name('api.sliders');
    });
});

// Health Check Routes (Public - No Authentication Required)
Route::get('health', [HealthController::class, 'index'])->name('api.health');
Route::get('health/detailed', [HealthController::class, 'detailed'])->name('api.health.detailed');
