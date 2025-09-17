<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * CODE STRUCTURE SUMMARY:
 * CacheWarmupService
 * Warms up application caches
 * Warm up configuration cache
 * Warm up route cache
 * Warm up view cache
 * Warm up critical queries
 * Warm up specific application caches
 * Get cache warmup status
 * Check if config is cached
 * Check if routes are cached
 * Check if views are cached
 * Clear all caches before warming up
 */
class CacheWarmupService
{
    /* Warm up all caches */
    /**
     * @return array{
     *   config: bool,
     *   routes: bool,
     *   views: bool,
     *   queries: bool
     * }
     */
    public function warmupAll(): array
    {
        $results = [];
        Log::info('Starting cache warmup process');
        $results['config'] = $this->warmupConfigCache();
        $results['routes'] = $this->warmupRouteCache();
        $results['views'] = $this->warmupViewCache();
        $results['queries'] = $this->warmupQueryCache();
        Log::info('Cache warmup process completed', $results);

        return $results;
    }

    /* Warm up configuration cache */
    public function warmupConfigCache(): bool
    {
        try {
            Artisan::call('config:cache');
            Log::info('Configuration cache warmed up successfully');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to warm up config cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /* Warm up route cache */
    public function warmupRouteCache(): bool
    {
        try {
            Artisan::call('route:cache');
            Log::info('Route cache warmed up successfully');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to warm up route cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /* Warm up view cache */
    public function warmupViewCache(): bool
    {
        try {
            Artisan::call('view:cache');
            Log::info('View cache warmed up successfully');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to warm up view cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /* Warm up critical queries */
    public function warmupQueryCache(): bool
    {
        try {
            $warmedQueries = 0;
            // Warm up user count
            $userCount = QueryCacheService::remember(
                'users.count',
                fn () => User::count(),
                3600,
                ['users']
            );
            $warmedQueries++;
            // Warm up recent users
            $recentUsers = QueryCacheService::remember(
                'users.recent.10',
                fn () => User::latest()->take(10)->get(['id', 'name', 'email', 'created_at']),
                1800,
                ['users']
            );
            $warmedQueries++;
            // Warm up active users (if you have a way to determine this)
            $activeUsers = QueryCacheService::remember(
                'users.active.count',
                fn () => User::whereNotNull('email_verified_at')->count(),
                3600,
                ['users']
            );
            $warmedQueries++;
            // Add more critical queries based on your application needs
            // Example: Popular content, frequently accessed data, etc.
            Log::info('Query cache warmed up successfully', [
                'queries_warmed' => $warmedQueries,
                'user_count' => $userCount,
                'recent_users_count' => $recentUsers->count(),
                'active_users_count' => $activeUsers,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to warm up query cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /* Warm up specific application caches */
    public function warmupApplicationCache(): bool
    {
        try {
            $warmedItems = 0;
            // Cache application settings if you have them
            $settings = QueryCacheService::remember(
                'app.settings',
                function () {
                    // Replace with your actual settings logic
                    return [
                        'app_name' => config('app.name'),
                        'app_version' => config('app.version', '1.0.0'),
                        'timezone' => config('app.timezone'),
                        'locale' => config('app.locale'),
                    ];
                },
                7200, // 2 hours
                ['settings']
            );
            $warmedItems++;

            // Cache navigation/menu data if you have it
            $navigation = QueryCacheService::remember(
                'app.navigation',
                function () {
                    // Replace with your actual navigation logic
                    return [
                        'main_menu' => [
                            ['label' => 'Dashboard', 'route' => 'dashboard'],
                            ['label' => 'Profile', 'route' => 'profile.edit'],
                        ],
                    ];
                },
                3600,
                ['navigation']
            );
            $warmedItems++;

            Log::info('Application cache warmed up successfully', [
                'items_warmed' => $warmedItems,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to warm up application cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /* Get cache warmup status */
    /**
     * @return array{
     *   config_cached: bool,
     *   routes_cached: bool,
     *   views_cached: bool,
     *   query_cache_stats: array<string, mixed>
     * }
     */
    public function getWarmupStatus(): array
    {
        return [
            'config_cached' => $this->isConfigCached(),
            'routes_cached' => $this->isRoutesCached(),
            'views_cached' => $this->isViewsCached(),
            'query_cache_stats' => QueryCacheService::getStats(),
        ];
    }

    /* Check if config is cached */
    private function isConfigCached(): bool
    {
        return file_exists(base_path('bootstrap/cache/config.php'));
    }

    /* Check if routes are cached */
    private function isRoutesCached(): bool
    {
        return file_exists(base_path('bootstrap/cache/routes-v7.php'));
    }

    /* Check if views are cached */
    private function isViewsCached(): bool
    {
        $viewCachePath = storage_path('framework/views');

        return is_dir($viewCachePath) && count(glob($viewCachePath.'/*.php')) > 0;
    }

    /* Clear all caches before warming up */
    /**
     * @return array{
     *   clear: bool,
     *   config: bool,
     *   routes: bool,
     *   views: bool,
     *   queries: bool
     * }
     */
    public function clearAndWarmup(): array
    {
        $results = [];

        Log::info('Starting clear and warmup process');

        // Clear all caches first
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            QueryCacheService::clearAll();

            $results['clear'] = true;
            Log::info('All caches cleared successfully');
        } catch (\Exception $e) {
            $results['clear'] = false;
            $results['config'] = false;
            $results['routes'] = false;
            $results['views'] = false;
            $results['queries'] = false;
            Log::error('Failed to clear caches', ['error' => $e->getMessage()]);

            return $results;
        }

        // Now warm up
        $results = array_merge($results, $this->warmupAll());

        return $results;
    }
}
