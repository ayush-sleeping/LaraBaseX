<?php

namespace App\Console\Commands;

use App\Services\CacheWarmupService;
use App\Services\QueryCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * CODE STRUCTURE SUMMARY:
 * name and signature of the console command
 * console command description
 * execute the console command
 * Show current cache status
 * Clear all caches
 * Optimize caches for production
 * Warm up caches
 */
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

    /* Execute the console command. */
    public function handle(): int
    {
        $action = $this->argument('action');
        $force = $this->option('force');

        $this->info('🚀 LaraBaseX Cache Management System');
        $this->info('=====================================');

        switch ($action) {
            case 'status':
                return $this->showCacheStatus();

            case 'warm':
                return $this->warmCache();

            case 'clear':
                return $this->clearCache($force);

            case 'optimize':
                return $this->optimizeCache();

            case 'all':
                $this->clearCache($force);
                $this->optimizeCache();
                $this->warmCache();

                return Command::SUCCESS;

            default:
                $this->error("❌ Invalid action: {$action}");
                $this->info('Available actions: status, warm, clear, optimize, all');

                return Command::FAILURE;
        }
    }

    /* Show current cache status */
    private function showCacheStatus(): int
    {
        $this->info('📊 Current Cache Status:');
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
                $this->info('✅ Cache Connection: Working');
            } else {
                $this->error('❌ Cache Connection: Failed (Read/Write Issue)');
            }
        } catch (\Exception $e) {
            $this->error('❌ Cache Connection: Failed - '.$e->getMessage());
        }

        // Check if caches are enabled
        $this->newLine();
        $this->info('📋 Cache Status:');

        $configCached = file_exists(base_path('bootstrap/cache/config.php'));
        $this->info('Config Cache: '.($configCached ? '✅ Enabled' : '❌ Disabled'));

        $routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        $this->info('Routes Cache: '.($routesCached ? '✅ Enabled' : '❌ Disabled'));

        $viewsCached = count(glob(storage_path('framework/views/*.php'))) > 0;
        $this->info('Views Cache: '.($viewsCached ? '✅ Enabled' : '❌ Disabled'));

        // Query Cache Statistics
        $queryStats = QueryCacheService::getStats();
        $this->info('Query Cache Driver: '.$queryStats['driver']);
        $this->info('Query Cache Tags Support: '.($queryStats['supports_tags'] ? '✅ Yes' : '❌ No'));
        $this->info('Query Cache Keys: '.$queryStats['query_cache_keys']);

        // Cache Size (if Redis)
        if ($cacheDriver === 'redis') {
            try {
                $this->info('Total Cache Keys: '.$queryStats['total_keys']);
            } catch (\Exception $e) {
                $this->warn('⚠️  Could not get cache statistics: '.$e->getMessage());
            }
        }

        return Command::SUCCESS;
    }

    /* Clear all caches */
    private function clearCache(bool $force = false): int
    {
        if (! $force) {
            if (! $this->confirm('🗑️  Clear all caches? This will impact performance temporarily.')) {
                $this->info('Cache clear cancelled.');

                return Command::SUCCESS;
            }
        }

        $this->info('🗑️  Clearing all caches...');

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('event:clear');

        QueryCacheService::clearAll();

        $this->info('✅ All caches cleared successfully!');

        return Command::SUCCESS;
    }

    /* Optimize caches for production */
    private function optimizeCache(): int
    {
        $this->info('⚡ Optimizing caches for production...');

        $environment = app()->environment();

        if ($environment === 'production') {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            Artisan::call('event:cache');

            $this->info('✅ Production caches optimized!');
        } else {
            $this->warn('⚠️  Cache optimization skipped - not in production environment');
            $this->info("Current environment: {$environment}");
        }

        return Command::SUCCESS;
    }

    /* Warm up caches */
    private function warmCache(): int
    {
        $this->info('🔥 Warming up caches...');

        $warmupService = new CacheWarmupService;
        $results = $warmupService->warmupAll();

        $this->info('📊 Warmup Results:');
        foreach ($results as $cache => $success) {
            $status = $success ? '✅' : '❌';
            $this->info("   {$status} ".ucfirst($cache).' cache');
        }

        $appResults = $warmupService->warmupApplicationCache();
        $appStatus = $appResults ? '✅' : '❌';
        $this->info("   {$appStatus} Application cache");

        $status = $warmupService->getWarmupStatus();
        $this->info("\n📈 Current Cache Status:");
        $this->info('   Config Cached: '.($status['config_cached'] ? '✅' : '❌'));
        $this->info('   Routes Cached: '.($status['routes_cached'] ? '✅' : '❌'));
        $this->info('   Views Cached: '.($status['views_cached'] ? '✅' : '❌'));

        $queryStats = $status['query_cache_stats'];
        $this->info('   Query Cache Keys: '.$queryStats['query_cache_keys']);

        return Command::SUCCESS;
    }
}
