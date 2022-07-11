<?php

namespace App\Console\Commands;

use App\Models\Message;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class PurgeMessageList extends Command
{
    protected $keep_for = 30; //days

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges the messages table to keep it small.';

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
        $this->info("Removing messages older than {$this->keep_for} days");
        try {
            Message::where('created_at', '<', Carbon::today()->subDays($this->keep_for))->delete();
        } catch (Exception $e) {
            $this->error('Unable to remove old message entries');

            return;
        }

        $this->info('Messages successfully purged');
    }
}
