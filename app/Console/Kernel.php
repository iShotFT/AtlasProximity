<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\ScanRegion',
        'App\Console\Commands\TrackPlayers',
        'App\Console\Commands\TrackBoats',
        'App\Console\Commands\TrackMonitors',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Scan EU PVP (takes ~1 min)
        $schedule->command('atlascctv:scanregion eu pvp')->everyFiveMinutes()->environments([
            'staging',
            'production',
        ])->runInBackground();

        // Scan NA PVP (takes ~2 min)
        $schedule->command('atlascctv:scanregion na pvp')->everyFiveMinutes()->environments([
            'staging',
            'production',
        ])->runInBackground();

        // Track players that are marked as to-be-tracked
        $schedule->command('atlascctv:trackplayers')->everyMinute()->environments([
            'staging',
            'production',
        ]);

        // Track servers that have an active proximity alert
        $schedule->command('atlascctv:trackboats')->everyMinute()->environments([
            'staging',
            'production',
        ]);

        //        // Track monitoring alerts
        //        $schedule->command('atlascctv:trackmonitors')->everyMinute()->environments([
        //            'staging',
        //            'production',
        //        ])->withoutOverlapping();

        $schedule->command('telescope:prune')->daily();
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
