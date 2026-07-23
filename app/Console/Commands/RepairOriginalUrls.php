<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use Illuminate\Support\Facades\DB;

class RepairOriginalUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:repair-original-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repair legacy original_url that starts with /news/ by setting them to NULL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Original URL repair process...');

        $totalChecked = DB::table('news_cache')->count();

        // Get only records that start with /news/
        $invalidRecords = DB::table('news_cache')
            ->where('original_url', 'like', '/news/%')
            ->get();

        $totalRepaired = 0;

        foreach ($invalidRecords as $record) {
            // Update original_url to NULL without altering any other fields (slug, title, etc)
            DB::table('news_cache')
                ->where('id', $record->id)
                ->update(['original_url' => null]);
            
            $totalRepaired++;
        }

        $totalSkipped = $totalChecked - $totalRepaired;

        $this->info("Total record diperiksa : {$totalChecked}");
        $this->info("Total record diperbaiki: {$totalRepaired}");
        $this->info("Total record dilewati  : {$totalSkipped}");
    }
}
