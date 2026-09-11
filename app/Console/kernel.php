<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // protected function schedule(Schedule $schedule)
    // {
    //     // প্রতিদিন চালু থাকবে; command DB-configured দুই দিনে SMS dispatch করবে।
    //     $schedule->command('sms:monthly-orders')
    //         ->dailyAt('21:00')
    //         ->withoutOverlapping();
    // }

    // protected function commands(): void
    // {
    //     $this->load(__DIR__.'/Commands');

    //     require base_path('routes/console.php');
    // }
}