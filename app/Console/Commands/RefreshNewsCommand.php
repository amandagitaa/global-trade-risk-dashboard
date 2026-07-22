<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\News\NewsSyncService;
use Illuminate\Support\Facades\Log;

class RefreshNewsCommand extends Command
{
    protected $signature = 'news:refresh';
    protected $description = 'Refresh entire news cache and synchronize globally';

    public function handle(NewsSyncService $syncService)
    {
        $this->warn('Starting Full News Refresh. This may take a while...');
        Log::info('Console: Executing news:refresh');
        
        $syncService->refresh();
        
        $this->info('News Refresh completed successfully.');
        return Command::SUCCESS;
    }
}
