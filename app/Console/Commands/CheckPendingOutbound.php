<?php

namespace App\Console\Commands;

use App\Jobs\SyncOutboundStatus;
use App\Models\Message;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CheckPendingOutbound extends Command
{
    /**
     * The number of hours to check for pending messages
     */
    public int $hours = 24;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pending:outbound {--hours=24 : The number of hours to check for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempts to check and update any pending outbound messages against carrier status';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->hours = $this->option('hours');

        $this->info("Getting pending outbound messages in the last {$this->hours} hour(s)...");
        try {
            $messages = Message::where('created_at', '>=', Carbon::now()->subHours($this->hours))->where('direction', 'outbound')->whereIn('status', ['pending','queued', 'sent'])->get();
        } catch (Exception $e) {
            $this->error('Unable to get pending outbound messages');

            return;
        }

        $this->info($messages->count().' pending outbound message(s) found');

        foreach ($messages as $message) {
            SyncOutboundStatus::dispatch($message);
        }

        $this->info('Pending outbound messages updated');
    }
}
