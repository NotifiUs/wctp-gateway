<?php

namespace App\Console\Commands;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkPendingAsFailed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pending:failed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks all pending messages as failed so they will not continue to retry.';

    /**
     * How long until we give-up and fail a pending message
     */
    private int $failMessageTimeInHours = 5;

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
     * @return int
     */
    public function handle()
    {
        foreach (Message::where('status', 'pending')->where('direction', 'inbound')->get() as $msg) {
            $this->comment('Processing '.$msg->id);
            $this->info($msg->created_at);
            $this->info(Carbon::now());
            $this->info('Difference: '.Carbon::now()->diffInMinutes(Carbon::parse($msg->created_at)));

            if (Carbon::now()->diffInMinutes(Carbon::parse($msg->created_at)) > $this->failMessageTimeInHours) {
                $this->info('Failing...'.$msg->id);
                $msg->status = 'failed';
                $msg->failed_at = Carbon::now()->timezone('America/New_York');
                $msg->save();
            } else {
                $this->info('Ignoring...'.$msg->id);
            }
        }

        $this->info('Complete...!');

        return 0;
    }
}
