<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cache key for published posts
     */
    public static function publishedPostsCacheKey($page = 1, $search = '', $author = '', $sort = 'latest'): string
    {
        return "posts.published.{$page}.{$search}.{$author}.{$sort}";
    }

    /**
     * Get cached published posts with pagination
     */
    public static function getCachedPublishedPosts($page = 1, $search = '', $author = '', $sort = 'latest', $perPage = 12)
    {
        $cacheKey = self::publishedPostsCacheKey($page, $search, $author, $sort);

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($search, $author, $sort, $perPage) {
            $query = self::with('user')
                ->whereNotNull('published_at');

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%');
                });
            }

            // Apply author filter
            if ($author) {
                $query->whereHas('user', function ($q) use ($author) {
                    $q->where('name', 'like', '%'.$author.'%');
                });
            }

            // Apply sorting
            switch ($sort) {
                case 'oldest':
                    $query->orderBy('published_at', 'asc');
                    break;
                case 'title':
                    $query->orderBy('title', 'asc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('published_at', 'desc');
                    break;
            }

            return $query->paginate($perPage);
        });
    }

    /**
     * Get cached list of authors
     */
    public static function getCachedAuthors()
    {
        return Cache::remember('posts.authors', now()->addHours(2), function () {
            return self::with('user')
                ->whereNotNull('published_at')
                ->get()
                ->pluck('user.name')
                ->unique()
                ->sort()
                ->values();
        });
    }

    /**
     * Get cached individual post
     */
    public static function getCachedPost($id)
    {
        return Cache::remember("post.{$id}", now()->addHour(), function () use ($id) {
            return self::with('user')->find($id);
        });
    }

    /**
     * Clear post-related caches
     */
    public static function clearPostCaches()
    {
        // Clear all post-related cache keys
        Cache::forget('posts.authors');

        // Clear paginated posts cache (basic patterns)
        $patterns = ['posts.published.*'];
        foreach ($patterns as $pattern) {
            Cache::flush(); // In production, use more specific cache clearing
        }
    }

    /**
     * Boot method to clear caches when posts are modified
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearPostCaches();
        });

        static::deleted(function () {
            self::clearPostCaches();
        });
    }
}
