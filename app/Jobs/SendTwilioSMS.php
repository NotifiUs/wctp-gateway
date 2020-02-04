<?php

namespace App\Jobs;

use Exception;
use App\Message;
use App\Carrier;
use Carbon\Carbon;
use App\EnterpriseHost;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SendTwilioSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    protected $host, $carrier, $recipient, $message, $messageID, $reply_with;

    public function __construct( EnterpriseHost $host, Carrier $carrier, string $recipient, string $message, int $messageID, $reply_with  )
    {
        $this->host = $host;
        $this->carrier = $carrier;
        $this->recipient = $recipient;
        $this->message = $message;
        $this->messageID = $messageID;
        $this->reply_with = $reply_with;

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
            return false;
        }

        try{
            $from = $this->carrier->numbers()->inRandomOrder()->where('enabled', 1)->first();
            if( $from->getType() == 'PN')
            {
                $params = [
                    'from' => $from->e164,
                    'body' => $this->message
                ];
            }
            else
            {
                $params = [
                    'messagingServiceSid' => $from->identifier,
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
                get_class( $this ), 'error', json_encode([$e->getMessage(), $from]), null
            );
            return false;
        }

        SaveMessage::dispatch(
            $this->carrier->id,
            $from->id,
            "+1{$this->recipient}",
            $from->e164,
            encrypt( $this->message ),
            $this->messageID,
            Carbon::now(),
            $this->reply_with,
            $msg->sid,
            'outbound'
        );

        return true;
    }
}
