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
        
        $syncService->setCommand($this);
        
        try {
            $syncService->sync();
            $this->info('News Synchronization completed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('News Synchronization failed:');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
