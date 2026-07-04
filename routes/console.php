<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Nightly backups (DB dump + releases zip, 7 kept). Requires the scheduler
// to run: on cPanel add a cron entry for `php artisan schedule:run` (see
// README go-live checklist).
Schedule::command('backup:run')->dailyAt('03:30');
