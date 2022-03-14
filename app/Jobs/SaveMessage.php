<?php

namespace App\Jobs;

use Exception;
use App\Message;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SaveMessage implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;
    public int $timeout = 60;
    public int $uniqueFor = 3600;
    protected $carrier_id, $number_id, $enterprise_host_id, $to, $from, $message, $messageID, $submitted_at, $reply_with, $carrier_message_uid, $direction;

    public function __construct( $carrier_id, $number_id, $enterprise_host_id, $to, $from, $message, $messageID, $submitted_at, $reply_with, $carrier_message_uid, $direction )
    {
        $this->carrier_id = $carrier_id;
        $this->number_id = $number_id;
        $this->enterprise_host_id = $enterprise_host_id;
        $this->to = $to;
        $this->from = $from;
        $this->message = $message;
        $this->messageID = $messageID;
        $this->submitted_at = $submitted_at;
        $this->reply_with = $reply_with;
        $this->carrier_message_uid = $carrier_message_uid;
        $this->direction = $direction;
        $this->queue = 'messages';
    }

    public function handle()
    {
        try{
            $message = new Message;
            $message->carrier_id = $this->carrier_id;
            $message->number_id = $this->number_id;
            $message->enterprise_host_id = $this->enterprise_host_id;
            $message->to = $this->to;
            $message->from = $this->from;
            $message->message = $this->message;
            $message->messageID = $this->messageID;
            $message->submitted_at = $this->submitted_at;
            $message->processed_at = Carbon::now();
            $message->reply_with = $this->reply_with;
            $message->carrier_message_uid = $this->carrier_message_uid;
            $message->direction = $this->direction;
            $message->save();
        }
        catch( Exception $e ){
            $this->release(60 );
        }

        if( $message->direction == 'inbound' )
        {
            SubmitToEnterpriseHost::dispatch( $message );
        }
    }

    public function uniqueId()
    {
        return $this->messageID;
    }
}
