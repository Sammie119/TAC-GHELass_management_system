<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Absentee flag scan — every Monday at 6am
        $schedule->command('absentees:flag --threshold=3')
            ->weeklyOn(1, '06:00');

        // Event reminders — every day at 6pm
        $schedule->command('notifications:event-reminders')
            ->dailyAt('18:00');

        // Birthday messages — every day at 8am
        $schedule->command('notifications:birthdays')
            ->dailyAt('08:00');

        // Absentee follow-up notifications — every Monday at 9am
        $schedule->command('notifications:absentees')
            ->weeklyOn(1, '09:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
