<?php

namespace App\Traits;

use App\Services\QueryCacheService;

/**
 * CODE STRUCTURE SUMMARY:
 * Cacheable
 * Get cache TTL for this model
 * Get cache tags for this model
 * Remember a query result in cache
 * Remember a query result in cache forever
 * Forget a cached result
 * Flush all cache for this model
 * Build cache key for this model
 * Cache count query
 * Cache exists query
 * Cache first query
 * Cache latest records
 * Cache oldest records
 * Cache find by ID
 * Cache where query
 * Cache pluck query
 * Clear cache when model is saved
 * Clear cache for this model
 * Get cache statistics for this model
 */
trait Cacheable
{
    /* Get cache TTL for this model */
    public function getCacheTTL(): int
    {
        // Always true for User, but keep fallback for other models
        return $this->cacheTTL ?? 3600;
    }

    /* Get cache tags for this model */
    public function getCacheTags(): array
    {
        $modelName = strtolower(class_basename(static::class));
        // Always true for User, but keep fallback for other models
        $customTags = $this->cacheTags ?? [];

        return array_merge([$modelName], $customTags);
    }

    /**
     * Remember a query result in cache
     *
     * @template TCacheValue
     *
     * @param  \Closure():TCacheValue  $callback
     * @return TCacheValue
     */
    public static function cacheRemember(string $key, \Closure $callback, ?int $ttl = null): mixed
    {
        $instance = new self;
        $ttl = $ttl ?? $instance->getCacheTTL();

        return QueryCacheService::remember(
            $instance->buildCacheKey($key),
            $callback,
            $ttl,
            $instance->getCacheTags()
        );
    }

    /**
     * Remember a query result in cache forever
     *
     * @template TCacheValue
     *
     * @param  \Closure():TCacheValue  $callback
     * @return TCacheValue
     */
    public static function cacheRememberForever(string $key, \Closure $callback): mixed
    {
        $instance = new self;

        return QueryCacheService::rememberForever(
            $instance->buildCacheKey($key),
            $callback,
            $instance->getCacheTags()
        );
    }

    /* Forget a cached result */
    public static function cacheForget(string $key): bool
    {
        $instance = new self;

        return QueryCacheService::forget($instance->buildCacheKey($key));
    }

    /* Flush all cache for this model */
    public static function flushCache(): bool
    {
        $instance = new self;

        return QueryCacheService::flushTags($instance->getCacheTags());
    }

    /* Build cache key for this model */
    protected function buildCacheKey(string $key): string
    {
        $modelName = strtolower(class_basename(static::class));

        return "{$modelName}.{$key}";
    }

    /* Cache count query */
    public static function cachedCount(string $key = 'count', ?int $ttl = null): int
    {
        return static::cacheRemember($key, fn () => static::count(), $ttl);
    }

    /* Cache exists query */
    public static function cachedExists(string $key = 'exists', ?int $ttl = null): bool
    {
        return static::cacheRemember($key, fn () => static::exists(), $ttl);
    }

    /* Cache first query */
    public static function cachedFirst(string $key = 'first', ?int $ttl = null)
    {
        return static::cacheRemember($key, fn () => static::first(), $ttl);
    }

    /* Cache latest records */
    public static function cachedLatest(int $limit = 10, ?string $key = null, ?int $ttl = null)
    {
        $key = $key ?? "latest.{$limit}";

        return static::cacheRemember($key, fn () => static::latest()->take($limit)->get(), $ttl);
    }

    /* Cache oldest records */
    public static function cachedOldest(int $limit = 10, ?string $key = null, ?int $ttl = null)
    {
        $key = $key ?? "oldest.{$limit}";

        return static::cacheRemember($key, fn () => static::oldest()->take($limit)->get(), $ttl);
    }

    /* Cache find by ID */
    public static function cachedFind($id, ?string $key = null, ?int $ttl = null)
    {
        $key = $key ?? "find.{$id}";

        return static::cacheRemember($key, fn () => static::find($id), $ttl);
    }

    /* Cache where query */
    public static function cachedWhere(string $column, $operator = null, $value = null, ?string $key = null, ?int $ttl = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        $key = $key ?? "where.{$column}.".md5(serialize([$operator, $value]));

        return static::cacheRemember($key, fn () => static::where($column, $operator, $value)->get(), $ttl);
    }

    /* Cache pluck query */
    public static function cachedPluck(string $column, ?string $key = null, ?int $ttl = null)
    {
        $key = $key ?? "pluck.{$column}";

        return static::cacheRemember($key, fn () => static::pluck($column), $ttl);
    }

    /* Clear cache when model is saved */
    protected static function bootCacheable()
    {
        static::saved(function ($model) {
            $model->clearModelCache();
        });
        static::deleted(function ($model) {
            $model->clearModelCache();
        });
    }

    /* Clear cache for this model */
    public function clearModelCache(): bool
    {
        return static::flushCache();
    }

    /* Get cache statistics for this model */
    public static function getCacheStats(): array
    {
        $instance = new self;
        $stats = QueryCacheService::getStats();
        $stats['model'] = class_basename(static::class);
        $stats['cache_tags'] = $instance->getCacheTags();
        $stats['cache_ttl'] = $instance->getCacheTTL();

        return $stats;
    }
}
