<?php

namespace App\Console;

use Laravel\Horizon\Console\StatusCommand;
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
        StatusCommand::class,
        Commands\PurgeEventLog::class,
        Commands\CreateAdminUser::class,
        Commands\SendPendingInbound::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('eventlog:purge')->daily();
        $schedule->command('telescope:prune')->daily();
        $schedule->command('pending:inbound')->everyMinute();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

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
