<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Add News Scheduler
// Using config to prevent hardcoding frequency
$frequency = config('news.sync_frequency', '0 */6 * * *'); // Default every 6 hours

Schedule::command('news:sync')->cron($frequency);
