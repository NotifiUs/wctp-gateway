<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Message;
use Illuminate\Console\Command;
use App\Jobs\SubmitToEnterpriseHost;

class SendPendingInbound extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pending:inbound';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempts to send any pending inbound messages to their host';

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
        $this->info("Getting pending inbound messages...");
        try{
            $messages = Message::whereNull('delivered_at')->where('direction', '=', 'inbound' )->where('status', 'pending')->get();
        }
        catch( Exception $e )
        {
            $this->error("Unable to get pending inbound messages");
            return;
        }

        $this->info( $messages->count() . " pending inbound message(s) found");

        foreach( $messages as $message )
        {
            SubmitToEnterpriseHost::dispatch( $message );
        }

        $this->info("Pending inbound messages processed");
    }
}
