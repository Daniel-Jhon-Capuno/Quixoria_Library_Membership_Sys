<?php

namespace App\Console;

use App\Console\Commands\EscalateOverdueBooksCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run escalation check daily at midnight
        $schedule->command(EscalateOverdueBooksCommand::class)
            ->dailyAt('00:00')
            ->name('escalate-overdue-books')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
