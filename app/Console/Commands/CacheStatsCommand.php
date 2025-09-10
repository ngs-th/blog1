<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:stats {--clear : Clear cache statistics}';

    /**
     * The console command description.
     */
    protected $description = 'Display cache statistics and performance metrics';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('clear')) {
            $this->clearCacheStats();

            return self::SUCCESS;
        }

        $this->displayCacheStats();

        return self::SUCCESS;
    }

    /**
     * Display comprehensive cache statistics
     */
    private function displayCacheStats(): void
    {
        $this->info('📊 Cache Performance Statistics');
        $this->newLine();

        // Basic cache info
        $this->table(
            ['Setting', 'Value'],
            [
                ['Cache Driver', config('cache.default')],
                ['Cache Store', config('cache.stores.'.config('cache.default').'.driver')],
                ['Environment', app()->environment()],
            ]
        );

        $this->newLine();
        $this->info('🗂️  Cached Data Overview:');

        // Check if key caches exist
        $cacheChecks = [
            'posts.authors' => 'Authors List',
            'posts.stats' => 'Post Statistics',
            'posts.published.1...latest' => 'First Page Posts',
        ];

        foreach ($cacheChecks as $key => $description) {
            $exists = Cache::has($key) ? '✅' : '❌';
            $this->line("  {$exists} {$description}");
        }

        $this->newLine();

        // Performance metrics
        try {
            $stats = CacheService::getCachedPostStats();
            $authors = Post::getCachedAuthors();

            $this->info('📈 Performance Metrics:');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Published Posts', $stats['total_posts']],
                    ['Unique Authors', count($authors)],
                    ['Posts This Month', $stats['posts_this_month']],
                    ['Latest Post', $stats['latest_post_date'] ? $stats['latest_post_date']->format('Y-m-d H:i') : 'None'],
                ]
            );
        } catch (\Exception $e) {
            $this->error("❌ Failed to retrieve cache stats: {$e->getMessage()}");
        }

        $this->newLine();
        $this->info('💡 Tips:');
        $this->line('  • Run `php artisan cache:posts` to warm up post caches');
        $this->line('  • Use `php artisan cache:clear` to clear all caches');
        $this->line('  • Monitor cache hit rates in production for optimization');
    }

    /**
     * Clear cache statistics
     */
    private function clearCacheStats(): void
    {
        $this->info('🧹 Clearing cache statistics...');

        try {
            CacheService::clearAllPostCaches();
            $this->info('✅ Cache statistics cleared successfully!');
        } catch (\Exception $e) {
            $this->error("❌ Failed to clear cache: {$e->getMessage()}");
        }
    }
}
