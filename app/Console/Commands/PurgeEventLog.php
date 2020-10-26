<?php

namespace App\Console\Commands;

use Exception;
use App\EventLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeEventLog extends Command
{

    protected $keep_for = 30; //days

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventlog:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges the Event Log table to keep it small.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Removing event log entries older than {$this->keep_for} days");
        try{
            EventLog::where('created_at', '<', Carbon::today()->subDays( $this->keep_for ) )->delete();
        }
        catch( Exception $e )
        {
            $this->error("Unable to remove old log entries");
            return;
        }

        $this->info("Event log successfully purged");
    }
}
