<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache prefix for news data
     */
    protected const PREFIX = 'news_cache_';
    
    /**
     * Default TTL in seconds (1 hour)
     */
    protected const TTL = 3600;

    /**
     * Stores processed news data into the cache.
     * Note: This caches the latest representation of a single article if needed,
     * but usually we cache the aggregate queries.
     *
     * @param array $processedData
     */
    public function store(array $processedData): void
    {
        if (isset($processedData['original_url'])) {
            $key = self::PREFIX . 'url_' . md5($processedData['original_url']);
            Cache::put($key, $processedData, self::TTL);
        }
    }

    /**
     * Retrieves data from the cache by key.
     */
    public function get(string $key)
    {
        return Cache::get(self::PREFIX . $key);
    }

    /**
     * Remembers a closure's result in the cache.
     */
    public function remember(string $key, \Closure $callback, int $ttl = self::TTL)
    {
        return Cache::remember(self::PREFIX . $key, $ttl, $callback);
    }

    /**
     * Forgets a specific cache key.
     */
    public function forget(string $key): void
    {
        Cache::forget(self::PREFIX . $key);
    }

    /**
     * Clears all news-related caches.
     * Since Laravel doesn't support wildcard deletion easily without tags (which require Redis/Memcached),
     * we log this intent. If using redis, `Cache::tags('news')->flush()` could be used.
     */
    public function clear(): void
    {
        Log::info("CacheService: Cleared news caches.");
        // If specific keys are tracked, they would be cleared here.
        // For standard file cache, we can only clear known keys or clear entirely.
        // We will just clear known aggregate keys.
        Cache::forget(self::PREFIX . 'latest_dashboard');
        Cache::forget(self::PREFIX . 'global_stats');
    }

    /**
     * Refreshes aggregate caches proactively.
     */
    public function refresh(): void
    {
        $this->clear();
        Log::info("CacheService: Refreshed aggregate caches after sync.");
    }
}
