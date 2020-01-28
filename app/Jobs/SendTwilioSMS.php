<?php

namespace App\Jobs;

use Exception;
use App\Carrier;
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

    protected $host, $carrier, $recipient, $message;

    public function __construct( EnterpriseHost $host, Carrier $carrier, string $recipient, string $message  )
    {
        $this->host = $host;
        $this->carrier = $carrier;
        $this->recipient = $recipient;
        $this->message = $message;
    }

    public function handle()
    {
        try{
            $client = new TwilioClient(
                $this->host->twilio_account_sid,
                $this->host->twilio_auth_token
            );
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'info', json_encode($e->getMessage()), null
            );
            return false;
        }

        try{
            $client->messages->create(
                "+1{$this->recipient}",
                array(
                    'from' => $this->carrier->numbers()->first()->e164,
                    'body' => $this->message
                )
            );
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure sending message",
                get_class( $this ), 'info', json_encode($e->getMessage()), null
            );
            return false;
        }

        return true;
    }
}
