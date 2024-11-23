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
        // Schedule the subscription status update command to run daily
        $schedule->command('subscription:update-status-active')->daily();

        $schedule->command('subscription:resume-paused')->daily();
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // $this->load(__DIR__.'/Commands');

        // require base_path('routes/console.php');

        $this->load(__DIR__.'/Commands');

        // Register the command explicitly if needed
        // $this->command('subscription:update-status-active', \App\Console\Commands\UpdateSubscriptionStatusActive::class);
    
        require base_path('routes/console.php');
    }


    
    
}
