<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheWarmupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:posts {action=warmup : Action to perform (warmup, clear, stats)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage post-related caches (warmup, clear, or show stats)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'warmup':
                $this->warmupCaches();
                break;
            case 'clear':
                $this->clearCaches();
                break;
            case 'stats':
                $this->showCacheStats();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: warmup, clear, stats');

                return 1;
        }

        return 0;
    }

    /**
     * Warm up application caches
     */
    private function warmupCaches(): void
    {
        $this->info('Warming up post caches...');

        $startTime = microtime(true);

        try {
            CacheService::warmUpCaches();

            // Record when cache was last warmed up
            Cache::put('cache.last_warmup', now()->toDateTimeString());

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $this->info("✅ Cache warmup completed in {$duration}ms");
            $this->newLine();
            $this->info('Warmed up caches:');
            $this->line('  • Authors list');
            $this->line('  • Published posts (first page)');
            $this->line('  • Post statistics');
            $this->line('  • Popular posts');

        } catch (\Exception $e) {
            $this->error("❌ Cache warmup failed: {$e->getMessage()}");
        }
    }

    /**
     * Clear application caches
     */
    private function clearCaches(): void
    {
        $this->info('Clearing post caches...');

        if ($this->confirm('This will clear all post-related caches. Continue?', true)) {
            try {
                CacheService::clearAllPostCaches();
                $this->info('✅ Post caches cleared successfully');
            } catch (\Exception $e) {
                $this->error("❌ Cache clearing failed: {$e->getMessage()}");
            }
        } else {
            $this->info('Cache clearing cancelled');
        }
    }

    /**
     * Show cache statistics
     */
    private function showCacheStats(): void
    {
        $this->info('Cache Statistics:');
        $this->newLine();

        try {
            $stats = CacheService::getCacheStats();

            $this->table(
                ['Setting', 'Value'],
                [
                    ['Cache Driver', $stats['cache_driver']],
                    ['Cache Enabled', $stats['cache_enabled'] ? 'Yes' : 'No'],
                    ['Last Warmup', $stats['last_warmed_up']],
                ]
            );

            // Show some cached data info
            $this->newLine();
            $this->info('Cached Data:');

            $authorsCount = count(\App\Models\Post::getCachedAuthors());
            $this->line("  • Authors: {$authorsCount} cached");

            $postStats = CacheService::getCachedPostStats();
            $this->line("  • Total Posts: {$postStats['total_posts']}");
            $this->line("  • Posts This Month: {$postStats['posts_this_month']}");

        } catch (\Exception $e) {
            $this->error("❌ Failed to retrieve cache stats: {$e->getMessage()}");
        }
    }
}
