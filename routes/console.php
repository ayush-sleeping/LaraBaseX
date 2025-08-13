<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// -------------------------------------------------------------------------------------------------------- ::
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule backup to run daily at 2:00 AM
Schedule::command('backup:database --only-db')->dailyAt('02:00')->name('daily-db-backup');

// Schedule full backup (database + files) to run weekly on Sundays at 3:00 AM
Schedule::command('backup:database')->weeklyOn(0, '03:00')->name('weekly-full-backup');

// Clean old backups - keep only last 7 days of backups
Schedule::command('backup:clean')->dailyAt('04:00')->name('cleanup-old-backups');
