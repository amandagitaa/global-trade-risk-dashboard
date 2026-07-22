<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\News\NewsSyncService;
use Illuminate\Support\Facades\Log;

class SyncNewsCommand extends Command
{
    protected $signature = 'news:sync';
    protected $description = 'Synchronize latest global trade news from API';

    public function handle(NewsSyncService $syncService)
    {
        $this->info('Starting News Synchronization...');
        Log::info('Console: Executing news:sync');
        
        $syncService->sync();
        
        $this->info('News Synchronization completed successfully.');
        return Command::SUCCESS;
    }
}
