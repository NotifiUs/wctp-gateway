<?php

namespace App\Jobs;

use App\Jobs\LogEvent;
use App\Models\Message;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SaveMessage implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;

    public int $timeout = 60;

    public int $uniqueFor = 3600;

    public bool $failOnTimeout = true;

    protected int $carrier_id;

    protected int $number_id;

    protected int $enterprise_host_id;

    protected string $to;

    protected string $from;

    protected string $message;

    protected string $messageID;

    protected int|null $reply_with;

    protected string $carrier_message_uid;

    protected string $direction;

    protected string $status;

    //DateTime,Carbon\Carbon,Illuminate\Support\Carbon?
    protected $processed_at;

    protected $submitted_at;

    public function __construct($carrier_id, $number_id, $enterprise_host_id, $to, $from, $message, $messageID, $submitted_at, $reply_with, $carrier_message_uid, $direction, string $status = 'pending')
    {
        $this->onQueue('messages');
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
        $this->status = $status;
        $this->processed_at = Carbon::now();
    }

    public function handle()
    {
        $message = new Message;
        $message->carrier_id = $this->carrier_id;
        $message->number_id = $this->number_id;
        $message->enterprise_host_id = $this->enterprise_host_id;
        $message->to = $this->to;
        $message->from = $this->from;
        $message->message = $this->message;
        $message->messageID = $this->messageID;
        $message->submitted_at = $this->submitted_at;
        $message->processed_at =
        $message->reply_with = $this->reply_with;
        $message->carrier_message_uid = $this->carrier_message_uid;
        $message->direction = $this->direction;
        $message->status = $this->status;

        try {
            $message->save();
        } catch (Exception $e) {
            Log::error('Unable to save message: '.$e->getMessage());
            LogEvent::dispatch(
                'Unable to save message.',
                get_class($this), 'error', json_encode($e->getMessage()), null
            );
            $this->release(60);
        }

        if ($message->direction == 'inbound') {
            SubmitToEnterpriseHost::dispatch($message);
        }
    }

    public function uniqueId(): string
    {
        return $this->carrier_message_uid;
    }
}
