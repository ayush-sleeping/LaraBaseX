<?php

namespace App\Console\Commands;

use App\Services\QueryCacheService;
use App\Services\CacheWarmupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CacheManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:management
                           {action=status : The action to perform (status|warm|clear|optimize|all)}
                           {--force : Force the operation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprehensive cache management for LaraBaseX';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $force = $this->option('force');

        $this->info("🚀 LaraBaseX Cache Management System");
        $this->info("=====================================");

        switch ($action) {
            case 'status':
                $this->showCacheStatus();
                break;

            case 'warm':
                $this->warmCache();
                break;

            case 'clear':
                $this->clearCache($force);
                break;

            case 'optimize':
                $this->optimizeCache();
                break;

            case 'all':
                $this->clearCache($force);
                $this->optimizeCache();
                $this->warmCache();
                break;

            default:
                $this->error("❌ Invalid action: {$action}");
                $this->info("Available actions: status, warm, clear, optimize, all");
                return 1;
        }

        return 0;
    }

    /**
     * Show current cache status
     */
    private function showCacheStatus()
    {
        $this->info("📊 Current Cache Status:");
        $this->newLine();

        // Cache Driver Info
        $cacheDriver = config('cache.default');
        $this->info("🔧 Cache Driver: {$cacheDriver}");

        // Cache Store Info
        if ($cacheDriver === 'redis') {
            $redisHost = config('database.redis.cache.host', 'N/A');
            $redisPort = config('database.redis.cache.port', 'N/A');
            $redisDb = config('database.redis.cache.database', 'N/A');
            $this->info("🔗 Redis Connection: {$redisHost}:{$redisPort} (DB: {$redisDb})");
        }

        // Test Cache Connection
        try {
            Cache::put('cache_test', 'test_value', 60);
            $testValue = Cache::get('cache_test');
            Cache::forget('cache_test');

            if ($testValue === 'test_value') {
                $this->info("✅ Cache Connection: Working");
            } else {
                $this->error("❌ Cache Connection: Failed (Read/Write Issue)");
            }
        } catch (\Exception $e) {
            $this->error("❌ Cache Connection: Failed - " . $e->getMessage());
        }

        // Check if caches are enabled
        $this->newLine();
        $this->info("📋 Cache Status:");

        $configCached = file_exists(base_path('bootstrap/cache/config.php'));
        $this->info("Config Cache: " . ($configCached ? "✅ Enabled" : "❌ Disabled"));

        $routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        $this->info("Routes Cache: " . ($routesCached ? "✅ Enabled" : "❌ Disabled"));

        $viewsCached = count(glob(storage_path('framework/views/*.php'))) > 0;
        $this->info("Views Cache: " . ($viewsCached ? "✅ Enabled" : "❌ Disabled"));

        // Query Cache Statistics
        $queryStats = QueryCacheService::getStats();
        $this->info("Query Cache Driver: " . $queryStats['driver']);
        $this->info("Query Cache Tags Support: " . ($queryStats['supports_tags'] ? "✅ Yes" : "❌ No"));
        $this->info("Query Cache Keys: " . $queryStats['query_cache_keys']);

        // Cache Size (if Redis)
        if ($cacheDriver === 'redis') {
            try {
                $this->info("Total Cache Keys: " . $queryStats['total_keys']);
            } catch (\Exception $e) {
                $this->warn("⚠️  Could not get cache statistics: " . $e->getMessage());
            }
        }
    }

    /**
     * Clear all caches
     */
    private function clearCache($force = false)
    {
        if (!$force) {
            if (!$this->confirm('🗑️  Clear all caches? This will impact performance temporarily.')) {
                $this->info("Cache clear cancelled.");
                return;
            }
        }

        $this->info("🗑️  Clearing all caches...");

        // Clear Application Cache
        $this->info("   - Clearing application cache...");
        Artisan::call('cache:clear');

        // Clear Query Cache
        $this->info("   - Clearing query cache...");
        QueryCacheService::clearAll();

        // Clear Config Cache
        $this->info("   - Clearing config cache...");
        Artisan::call('config:clear');

        // Clear Route Cache
        $this->info("   - Clearing route cache...");
        Artisan::call('route:clear');

        // Clear View Cache
        $this->info("   - Clearing view cache...");
        Artisan::call('view:clear');

        // Clear Event Cache
        $this->info("   - Clearing event cache...");
        Artisan::call('event:clear');

        $this->info("✅ All caches cleared successfully!");
    }

    /**
     * Optimize caches for production
     */
    private function optimizeCache()
    {
        $this->info("⚡ Optimizing caches for production...");

        // Only cache in production environment
        $environment = app()->environment();

        if ($environment === 'production') {
            // Cache Config
            $this->info("   - Caching configuration...");
            Artisan::call('config:cache');

            // Cache Routes
            $this->info("   - Caching routes...");
            Artisan::call('route:cache');

            // Cache Views
            $this->info("   - Caching views...");
            Artisan::call('view:cache');

            // Cache Events
            $this->info("   - Caching events...");
            Artisan::call('event:cache');

            $this->info("✅ Production caches optimized!");
        } else {
            $this->warn("⚠️  Cache optimization skipped - not in production environment");
            $this->info("Current environment: {$environment}");
        }
    }

    /**
     * Warm up caches
     */
    private function warmCache()
    {
        $this->info("🔥 Warming up caches...");

        $warmupService = new CacheWarmupService();
        $results = $warmupService->warmupAll();

        $this->info("📊 Warmup Results:");
        foreach ($results as $cache => $success) {
            $status = $success ? '✅' : '❌';
            $this->info("   {$status} " . ucfirst($cache) . " cache");
        }

        // Also warm up application-specific caches
        $appResults = $warmupService->warmupApplicationCache();
        $appStatus = $appResults ? '✅' : '❌';
        $this->info("   {$appStatus} Application cache");

        $this->info("🎉 Cache warmup completed!");

        // Show warmup status
        $status = $warmupService->getWarmupStatus();
        $this->info("\n📈 Current Cache Status:");
        $this->info("   Config Cached: " . ($status['config_cached'] ? '✅' : '❌'));
        $this->info("   Routes Cached: " . ($status['routes_cached'] ? '✅' : '❌'));
        $this->info("   Views Cached: " . ($status['views_cached'] ? '✅' : '❌'));

        $queryStats = $status['query_cache_stats'];
        $this->info("   Query Cache Keys: " . $queryStats['query_cache_keys']);
    }
}
