<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// ── Laravel Scheduler ────────────────────────────────────────────────────────
// cPanel cron: * * * * * php /home/user/heavenlymatch/artisan schedule:run >> /dev/null 2>&1

// Nightly match score computation at 02:00 BDT (20:00 UTC)
Schedule::job(new \App\Jobs\ComputeMatchScoresJob)
    ->dailyAt('20:00')
    ->withoutOverlapping();

// Deactivate expired profile boosts every 30 minutes
Schedule::command('boosts:expire')
    ->everyThirtyMinutes()
    ->withoutOverlapping();

// Daily match notification emails at 09:00 BDT (03:00 UTC)
Schedule::command('notify:daily-matches')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Weekly re-engagement email — Sunday 10:00 BDT (04:00 UTC)
Schedule::command('notify:reengagement')
    ->weeklyOn(0, '04:00')
    ->withoutOverlapping();

// Purge soft-deleted messages older than 90 days
Schedule::command('messages:purge --days=90')
    ->weekly()
    ->withoutOverlapping();
