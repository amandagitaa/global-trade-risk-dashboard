<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\NewsProviderInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NewsHealthCommand extends Command
{
    protected $signature = 'news:health';
    protected $description = 'Run a health check on the News Architecture components';

    public function handle(NewsProviderInterface $apiProvider)
    {
        $this->info('Starting News Architecture Health Check...');
        
        // 1. API Check
        $this->info('- Checking API Provider...');
        if ($apiProvider->healthCheck()) {
            $this->line('  [OK] API Provider configured correctly.');
        } else {
            $this->error('  [FAIL] API Provider configuration missing or invalid.');
        }

        // 2. Database Check
        $this->info('- Checking Database (news_cache table)...');
        try {
            DB::table('news_cache')->first();
            $this->line('  [OK] Database table accessible.');
        } catch (\Exception $e) {
            $this->error('  [FAIL] Database table error: ' . $e->getMessage());
        }

        // 3. Cache Check
        $this->info('- Checking Cache System...');
        try {
            Cache::put('news_health_test', '1', 10);
            if (Cache::get('news_health_test') === '1') {
                $this->line('  [OK] Cache system functioning normally.');
            } else {
                $this->error('  [FAIL] Cache system failed to retrieve value.');
            }
        } catch (\Exception $e) {
            $this->error('  [FAIL] Cache system error: ' . $e->getMessage());
        }
        
        $this->info('Health Check completed.');
        return Command::SUCCESS;
    }
}
