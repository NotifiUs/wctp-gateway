<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Horizon\Console\StatusCommand;

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
        Commands\PurgeMessageList::class,
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
        $schedule->command('messages:purge')->daily();
        $schedule->command('eventlog:purge')->daily();
        $schedule->command('telescope:prune --hours=1')->hourly();
        $schedule->command('pending:inbound')->everyMinute();
        $schedule->command('pending:outbound --hours=1')->everyMinute();
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
