<?php

namespace App\Console\Commands;

use Exception;
use App\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkMessagesSent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:deliver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks outbound messages that are pending as sent. This should only be used for troubleshooting outbound message status sync.';

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
        $this->info("Marking pending messages as sent.");
        try{
            $messages = Message::where('status', 'pending')->where('direction', 'outbound')->get();
        }
        catch( Exception $e )
        {
            $this->error("Unable to update pending messages.");
            return;
        }

        foreach( $messages as $message )
        {
            $message->status = 'sent';
            $message->save();
        }

        $this->info("Messages successfully marked as sent.");
    }
}
