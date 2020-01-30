<?php

namespace App\Jobs;

use Exception;
use App\Carrier;
use App\EnterpriseHost;
use Illuminate\Bus\Queueable;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendThinqSMS implements ShouldQueue
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
            $thinq = new Guzzle([
                'timeout' => 10.0,
                'base_uri' => 'https://api.thinq.com',
                'auth' => [ $this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)],
                'headers' => [ 'content-type' => 'application/json' ],
                'json' => [
                    'from_did' => substr( $this->carrier->numbers()->first()->e164, 2),
                    'to_did' => $this->recipient,
                    'message' => $this->message,
                ],
            ]);
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failed decrypting carrier api token",
                get_class( $this ), 'info', json_encode($this->carrier->toArray()), null
            );
            return false;
        }

        $result = $thinq->post("account/{$this->carrier->thinq_account_id}/product/origination/sms/send");
        if( $result->getStatusCode() != 200 )
        {
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'info', json_encode($result->getReasonPhrase()), null
            );
            return false;
        }
        $body = $result->getBody();
        $json = $body->getContents();
        $arr = json_decode( $json, true );
        if( ! isset( $arr['guid']))
        {
            LogEvent::dispatch(
                "No message GUID returned from carrier",
                get_class( $this ), 'info', json_encode($arr), null
            );
            return false;
        }

        return true;
    }
}
