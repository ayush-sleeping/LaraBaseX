<?php

use App\Http\Controllers\Backend\AnalyticsController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\EnquiryController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Models\User;
use App\Services\QueryCacheService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () { Inertia::render('dashboard');})->name('dashboard');
});
 */

// Dashboard route - redirects to admin dashboard
// -------------------------------------------------------------------------------------------------------- ::
/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard.index');
    })->name('dashboard');
}); */

// -------------------------------------------------------------------------------------------------------- ::
Route::middleware(['auth', 'verified', 'admin', 'preventBackHistory'])->group(function () {

    // Main dashboard route - direct access
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin prefix for all backend routes
    // -------------------------------------------------------------------------------------------------------- ::
    Route::prefix('admin')->name('admin.')->group(function () {

        // Users
        Route::resource('users', UserController::class);
        Route::post('users/data', [UserController::class, 'data'])->name('users.data');
        Route::post('users/list', [UserController::class, 'list'])->name('users.list');
        Route::post('users/change-status', [UserController::class, 'changeStatus'])->name('users.change.status');

        // Roles
        Route::resource('roles', RoleController::class);
        Route::post('roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::post('roles/list', [RoleController::class, 'list'])->name('roles.list');

        // Permissions
        Route::get('roles/{role}/permission/show', [RoleController::class, 'permissionsShow'])->name('roles.permissions.show');
        Route::post('roles/{role}/permission/update', [RoleController::class, 'permissionsUpdate'])->name('roles.permissions.update');

        // Employees
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
        Route::post('employees/list', [EmployeeController::class, 'list'])->name('employees.list');
        Route::post('employees/change-status', [EmployeeController::class, 'changeStatus'])->name('employees.change.status');

        // Enquiries
        Route::resource('enquiries', EnquiryController::class);
        Route::post('enquiries/data', [EnquiryController::class, 'data'])->name('enquiries.data');
        Route::post('enquiries/list', [EnquiryController::class, 'list'])->name('enquiries.list');
        Route::post('enquiries/{enquiry}/remark', [EnquiryController::class, 'updateRemark'])->name('enquiries.update.remark');

        // Analytics
        Route::resource('analytics', AnalyticsController::class);
        // End of File
    });
});

// Cache test route
// -------------------------------------------------------------------------------------------------------- ::
Route::get('/test-cache', function () {
    $start = microtime(true);

    // Test cached user count
    $userCount = User::cachedCount();

    // Test cached recent users
    $recentUsers = User::cachedLatest(5, 'recent.5');

    // Test query cache service directly
    $appSettings = QueryCacheService::remember(
        'app.test.settings',
        fn () => [
            'name' => config('app.name'),
            'env' => app()->environment(),
            'version' => config('app.version', '1.0.0'),
            'timezone' => config('app.timezone'),
        ],
        600
    );

    $end = microtime(true);
    $executionTime = round(($end - $start) * 1000, 2);

    return response()->json([
        'message' => 'Cache test completed',
        'execution_time_ms' => $executionTime,
        'data' => [
            'user_count' => $userCount,
            'recent_users_count' => $recentUsers->count(),
            'app_settings' => $appSettings,
        ],
        'cache_stats' => QueryCacheService::getStats(),
        'user_cache_stats' => User::getCacheStats(),
    ]);
})->name('test.cache');
