<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\NewsCache;

class CleanDummyNews extends Command
{
    protected $signature = 'news:clean-dummy {--force-truncate : Force truncate the entire table instead of trying to find dummy patterns}';
    protected $description = 'Clean up dummy news data from news_cache table and optionally resync';

    public function handle()
    {
        $this->info("Starting News Data Cleanup...");

        if ($this->option('force-truncate')) {
            $this->truncateAndSync();
            return;
        }

        // 1. Identify Dummy Data
        // Real API data comes from 'newsdata', 'gnews', 'rss'. Dummy data usually lacks these or has specific sources/URLs
        
        $realProviders = ['newsdata', 'gnews', 'rss'];
        
        $dummyQuery = NewsCache::where(function($q) use ($realProviders) {
            $q->whereNotIn('provider', $realProviders)
              ->orWhereNull('provider')
              ->orWhere('source', 'like', '%dummy%')
              ->orWhere('source', 'like', '%seeder%')
              ->orWhere('url', 'like', '%example.com%')
              ->orWhere('original_url', 'like', '/news/%'); // Legacy dummy pattern
        });

        $dummyCount = $dummyQuery->count();
        $realCount = NewsCache::whereIn('provider', $realProviders)->count();

        $this->info("Found {$dummyCount} potential dummy records.");
        $this->info("Found {$realCount} real API records.");

        if ($dummyCount > 0) {
            if ($this->confirm("Do you want to delete these {$dummyCount} dummy records safely?")) {
                $dummyQuery->delete();
                $this->info("Successfully deleted {$dummyCount} dummy records.");
                
                if ($this->confirm("Do you want to run news:sync to fetch fresh data for all categories?")) {
                    $this->call('news:sync');
                }
            } else {
                $this->info("Cleanup aborted by user.");
            }
        } else {
            $this->info("No obvious dummy data found using safe identification patterns.");
            
            $this->warn("If you are still seeing dummy data in the dashboard, it means they cannot be safely distinguished from real data.");
            if ($this->confirm("Do you want to TRUNCATE the entire news_cache table and resync fresh data from API? (This will delete all {$realCount} real records too)")) {
                $this->truncateAndSync();
            }
        }
    }

    protected function truncateAndSync()
    {
        $this->warn("Truncating news_cache table...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        NewsCache::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->info("Table truncated successfully. Starting fresh sync...");
        $this->call('news:sync');
        $this->info("Fresh sync completed!");
    }
}
