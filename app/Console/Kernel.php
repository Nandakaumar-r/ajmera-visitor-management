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
        // Schedule your commands here
        $schedule->command('birthday:photo-request')->dailyAt('09:00'); // Photo request email, one day before birthday
        $schedule->command('birthday:greetings')->dailyAt('09:00'); // Birthday greeting email, on birthday
        
        // Send WFH attendance reminders at 5 PM daily
        $schedule->command('attendance:wfh-reminders')
                ->dailyAt('17:00')
                ->weekdays();

        // Run at 00:01 on the first day of each month
        $schedule->command('leaves:update-monthly-balance')
            ->monthlyOn(1, '00:01')
            ->appendOutputTo(storage_path('logs/leave-balance-updates.log'));
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
