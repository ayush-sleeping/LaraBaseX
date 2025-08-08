<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use App\Services\QueryCacheService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Home page route
// -------------------------------------------------------------------------------------------------------- ::
Route::get('/', function () { return Inertia::render('welcome'); })->name('home');

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
        fn() => [
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

// Include route files
// -------------------------------------------------------------------------------------------------------- ::
require __DIR__.'/settings.php';    // User settings routes
require __DIR__.'/auth.php';        // Authentication routes
require __DIR__ . '/backend.php';   // Admin panel routes
require __DIR__ . '/frontend.php';  // Frontend routes
