<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
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
        // Update expired QR codes every 5 minutes
        $schedule->command('qrcode:update-expired')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->onSuccess(function () {
                Log::info('QR code expiry update completed successfully via scheduler');
            })
            ->onFailure(function () {
                Log::error('QR code expiry update failed via scheduler');
            });

        // Optional: Run a more comprehensive check daily
        $schedule->command('qrcode:update-expired --force')
            ->daily()
            ->at('02:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->description('Daily comprehensive QR code expiry check');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
