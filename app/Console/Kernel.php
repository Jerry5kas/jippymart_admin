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
        // Auto-schedule functionality has been removed to prevent resource issues
        // Restaurant status management is now handled manually through the admin interface
        
        // Generate sitemap daily at 2 AM
        $schedule->command('generate:sitemap')->dailyAt('02:00');
        
        // Alternative scheduling options (uncomment if needed):
        
        // Every 6 hours
        // $schedule->command('generate:sitemap')->everySixHours();
        
        // Twice daily (2 AM and 2 PM)
        // $schedule->command('generate:sitemap')->twiceDaily(2, 14);
        
        // Every 12 hours
        // $schedule->command('generate:sitemap')->twiceDaily();
        
        // Weekly (Sundays at 2 AM)
        // $schedule->command('generate:sitemap')->weeklyOn(0, '02:00');
        
        // Only when content changes (manual trigger)
        // Remove the schedule line above and trigger manually via admin panel
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
