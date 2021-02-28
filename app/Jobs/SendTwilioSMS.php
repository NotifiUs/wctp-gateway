<?php

namespace App\Jobs;

use Exception;
use App\Carrier;
use Carbon\Carbon;
use App\EnterpriseHost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SendTwilioSMS implements ShouldQueue
{

    public $tries = 10;
    public $timeout = 60;
    public $uniqueFor = 3600;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    protected $host, $carrier, $recipient, $message, $messageID, $reply_with, $from;

    public function __construct( EnterpriseHost $host, Carrier $carrier, string $recipient, string $message, int $messageID, $reply_with  )
    {
        $this->queue = 'outbound';
        $this->host = $host;
        $this->carrier = $carrier;
        $this->recipient = $recipient;
        $this->message = $message;
        $this->messageID = $messageID;
        $this->reply_with = $reply_with;
        $this->from = $this->carrier->numbers()->inRandomOrder()->where('enabled', 1)->where('enterprise_host_id', $this->host->id )->first();
        if( is_null( $this->from ) )
        {
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'error', json_encode("No enabled numbers assigned to host"), null
            );
            $this->release(60 );
        }
    }

    public function handle()
    {
        try{
            $client = new TwilioClient(
                $this->carrier->twilio_account_sid,
                decrypt( $this->carrier->twilio_auth_token )
            );
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );
            $this->release(60 );
        }

        try{
            if( is_null( $this->from ) )
            {
                LogEvent::dispatch(
                    "Failure submitting message",
                    get_class( $this ), 'error', json_encode("No enabled numbers assigned to host"), null
                );
                $this->release(60 );
            }

            if( $this->from->getType() == 'PN')
            {
                $params = [
                    'from' => $this->from->e164,
                    'body' => $this->message
                ];
            }
            else
            {
                $params = [
                    'messagingServiceSid' => $this->from->identifier,
                    'body' => $this->message
                ];
            }

            $msg = $client->messages->create(
                "+1{$this->recipient}",
                $params
            );
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure sending message",
                get_class( $this ), 'error', json_encode([$e->getMessage(), $this->from]), null
            );
            $this->release(60 );
        }

        SaveMessage::dispatch(
            $this->carrier->id,
            $this->from->id,
            $this->host->id,
            "+1{$this->recipient}",
            $this->from->e164,
            encrypt( $this->message ),
            $this->messageID,
            Carbon::now(),
            $this->reply_with,
            $msg->sid,
            'outbound'
        );

        return;
    }
}
