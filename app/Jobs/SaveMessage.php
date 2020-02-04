<?php

namespace App\Jobs;

use Exception;
use App\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $carrier_id, $number_id, $to, $from, $message, $messageID, $submitted_at, $reply_with;


    public function __construct( $carrier_id, $number_id, $to, $from, $message, $messageID, $submitted_at, $reply_with )
    {
        $this->carrier_id = $carrier_id;
        $this->number_id = $number_id;
        $this->to = $to;
        $this->from = $from;
        $this->message = $message;
        $this->messageID = $messageID;
        $this->submitted_at = $submitted_at;
        $this->reply_with = $reply_with;
    }


    public function handle()
    {
        try{
            $message = new Message;
            $message->carrier_id = $this->carrier_id;
            $message->number_id = $this->number_id;
            $message->to = $this->to;
            $message->from = $this->from;
            $message->message = $this->message;
            $message->messageID = $this->messageID;
            $message->submitted_at = $this->submitted_at;
            $message->reply_with = $this->reply_with;
            $message->save();
        }
        catch( Exception $e ){
            return false;
        }

    }
}
