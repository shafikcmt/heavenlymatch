<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Nightly match score computation at 02:00 BDT (UTC+6 = 20:00 UTC)
        $schedule->job(new \App\Jobs\ComputeMatchScoresJob)
                 ->dailyAt('20:00')
                 ->onQueue('matching')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Deactivate expired profile boosts every 30 minutes
        $schedule->command('boosts:expire')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping();

        // Daily match emails at 09:00 BDT (03:00 UTC)
        $schedule->command('notify:daily-matches')
                 ->dailyAt('03:00')
                 ->withoutOverlapping();

        // Weekly inactive-user re-engagement email (Sunday 10:00 BDT)
        $schedule->command('notify:reengagement')
                 ->weeklyOn(0, '04:00')
                 ->withoutOverlapping();

        // Purge soft-deleted messages older than 90 days
        $schedule->command('messages:purge --days=90')
                 ->weekly()
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
