<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class CacheService
{
    /**
     * Cache durations in minutes
     */
    const CACHE_DURATIONS = [
        'posts_list' => 30,
        'post_detail' => 60,
        'authors_list' => 120,
        'view_cache' => 60,
        'stats_cache' => 15,
    ];

    /**
     * Cache a view with given parameters
     */
    public static function cacheView(string $viewName, array $data = [], ?int $minutes = null): string
    {
        $minutes = $minutes ?? self::CACHE_DURATIONS['view_cache'];
        $cacheKey = self::generateViewCacheKey($viewName, $data);

        return Cache::remember($cacheKey, now()->addMinutes($minutes), function () use ($viewName, $data) {
            return View::make($viewName, $data)->render();
        });
    }

    /**
     * Generate cache key for view
     */
    private static function generateViewCacheKey(string $viewName, array $data): string
    {
        $dataHash = md5(serialize($data));

        return "view.{$viewName}.{$dataHash}";
    }

    /**
     * Cache post statistics
     */
    public static function getCachedPostStats(): array
    {
        return Cache::remember('posts.stats', now()->addMinutes(self::CACHE_DURATIONS['stats_cache']), function () {
            return [
                'total_posts' => \App\Models\Post::whereNotNull('published_at')->count(),
                'total_authors' => \App\Models\Post::with('user')
                    ->whereNotNull('published_at')
                    ->distinct('user_id')
                    ->count('user_id'),
                'latest_post_date' => \App\Models\Post::whereNotNull('published_at')
                    ->latest('published_at')
                    ->value('published_at'),
                'posts_this_month' => \App\Models\Post::whereNotNull('published_at')
                    ->whereMonth('published_at', now()->month)
                    ->whereYear('published_at', now()->year)
                    ->count(),
            ];
        });
    }

    /**
     * Cache popular posts (most liked/bookmarked)
     */
    public static function getCachedPopularPosts(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("posts.popular.{$limit}", now()->addHours(2), function () use ($limit) {
            // Since we're using session-based likes/bookmarks, we'll use recent posts as popular
            // In a real app, you'd have a proper likes/bookmarks table
            return \App\Models\Post::with('user')
                ->whereNotNull('published_at')
                ->latest('published_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear all post-related caches
     */
    public static function clearAllPostCaches(): void
    {
        $patterns = [
            'posts.*',
            'post.*',
            'view.posts.*',
            'view.livewire.posts.*',
        ];

        // Clear specific cache keys
        Cache::forget('posts.stats');
        Cache::forget('posts.authors');

        // In production, you'd use cache tags or a more sophisticated approach
        // For now, we'll clear the entire cache when posts are modified
        Cache::flush();
    }

    /**
     * Warm up frequently accessed caches
     */
    public static function warmUpCaches(): void
    {
        // Warm up authors cache
        \App\Models\Post::getCachedAuthors();

        // Warm up first page of posts
        \App\Models\Post::getCachedPublishedPosts(1, '', '', 'latest', 12);

        // Warm up post statistics
        self::getCachedPostStats();

        // Warm up popular posts
        self::getCachedPopularPosts();
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        // This would require Redis or Memcached for detailed stats
        // For file cache, we'll return basic info
        return [
            'cache_driver' => config('cache.default'),
            'cache_enabled' => true,
            'last_warmed_up' => Cache::get('cache.last_warmup', 'Never'),
        ];
    }
}
