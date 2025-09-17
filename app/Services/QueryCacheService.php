<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * CODE STRUCTURE SUMMARY:
 * QueryCacheService
 * Default cache TTL in seconds (1 hour)
 * Cache key prefix
 * Cache a database query result
 * Cache forever (until manually cleared)
 * Forget a cached query
 * Flush cache by tags
 * Check if current cache driver supports tags
 * Get cache statistics
 * Clear all query cache
 * Generate cache key from query and parameters
 * Cache a model query
 */
class QueryCacheService
{
    /**
     * Default cache TTL in seconds (1 hour)
     */
    const DEFAULT_TTL = 3600;

    /**
     * Cache key prefix
     */
    const CACHE_PREFIX = 'query_cache:';

    /**
     * Cache a database query result
     *
     * @return mixed
     */
    /**
     * @template TCacheValue
     *
     * @param  \Closure():TCacheValue  $callback
     * @param  array<int, string>  $tags
     * @return TCacheValue
     */
    public static function remember(string $key, \Closure $callback, ?int $ttl = null, array $tags = [])
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;
        $cacheKey = self::CACHE_PREFIX.$key;

        try {
            if (! empty($tags) && self::supportsTags()) {
                return Cache::tags($tags)->remember($cacheKey, $ttl, $callback);
            }

            return Cache::remember($cacheKey, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning('Query cache failed, executing query directly', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return $callback();
        }
    }

    /**
     * Cache forever (until manually cleared)
     *
     * @return mixed
     */
    /**
     * @template TCacheValue
     *
     * @param  \Closure():TCacheValue  $callback
     * @param  array<int, string>  $tags
     * @return TCacheValue
     */
    public static function rememberForever(string $key, \Closure $callback, array $tags = [])
    {
        $cacheKey = self::CACHE_PREFIX.$key;

        try {
            if (! empty($tags) && self::supportsTags()) {
                return Cache::tags($tags)->rememberForever($cacheKey, $callback);
            }

            return Cache::rememberForever($cacheKey, $callback);
        } catch (\Exception $e) {
            Log::warning('Query cache (forever) failed, executing query directly', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return $callback();
        }
    }

    /* Forget a cached query */
    public static function forget(string $key): bool
    {
        $cacheKey = self::CACHE_PREFIX.$key;
        try {
            return Cache::forget($cacheKey);
        } catch (\Exception $e) {
            Log::warning('Failed to forget cache key', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /* Flush cache by tags */
    /**
     * @param  array<int, string>  $tags
     */
    public static function flushTags(array $tags): bool
    {
        if (! self::supportsTags()) {
            Log::warning('Cache tags not supported by current driver');

            return false;
        }
        try {
            Cache::tags($tags)->flush();

            return true;
        } catch (\Exception $e) {
            Log::warning('Failed to flush cache tags', [
                'tags' => $tags,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /* Check if current cache driver supports tags */
    public static function supportsTags(): bool
    {
        $driver = config('cache.default');

        return in_array($driver, ['redis', 'memcached']);
    }

    /* Get cache statistics */
    /**
     * @return array{
     *   driver: string,
     *   supports_tags: bool,
     *   total_keys: int,
     *   query_cache_keys: int
     * }
     */
    public static function getStats(): array
    {
        $driver = config('cache.default');
        $stats = [
            'driver' => $driver,
            'supports_tags' => self::supportsTags(),
            'total_keys' => 0,
            'query_cache_keys' => 0,
        ];
        if ($driver === 'redis') {
            try {
                $redis = app('redis.connection')->connection('cache');
                $allKeys = $redis->keys('*');
                $queryCacheKeys = $redis->keys(self::CACHE_PREFIX.'*');

                $stats['total_keys'] = count($allKeys);
                $stats['query_cache_keys'] = count($queryCacheKeys);
            } catch (\Exception $e) {
                Log::warning('Failed to get Redis cache stats', ['error' => $e->getMessage()]);
            }
        }

        return $stats;
    }

    /* Clear all query cache */
    public static function clearAll(): bool
    {
        try {
            $driver = config('cache.default');
            if ($driver === 'redis') {
                $redis = app('redis.connection')->connection('cache');
                $keys = $redis->keys(self::CACHE_PREFIX.'*');

                if (! empty($keys)) {
                    $redis->del($keys);
                }

                return true;
            }
            // For other drivers, we'd need to clear the entire cache
            Cache::flush();

            return true;
        } catch (\Exception $e) {
            Log::warning('Failed to clear query cache', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /* Generate cache key from query and parameters */
    /**
     * @param  array<int, mixed>  $bindings
     */
    public static function generateKey(string $query, array $bindings = []): string
    {
        return md5($query.serialize($bindings));
    }

    /* Cache a model query */
    /**
     * @param  array<int, mixed>  $parameters
     */
    public static function modelKey(string $model, string $method, array $parameters = [], ?int $ttl = null): string
    {
        $baseKey = class_basename($model).':'.$method;
        if (! empty($parameters)) {
            $baseKey .= ':'.md5(serialize($parameters));
        }

        return $baseKey;
    }
}
