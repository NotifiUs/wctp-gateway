<?php

namespace App\Jobs;

use Exception;
use App\Carrier;
use Carbon\Carbon;
use App\EnterpriseHost;
use Illuminate\Bus\Queueable;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendThinqSMS implements ShouldQueue, ShouldBeUnique
{
    public $tries = 10;
    public $timeout = 60;
    public $uniqueFor = 3600;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    protected $host, $carrier, $recipient, $message, $messageID, $reply_with, $from;

    public function __construct( EnterpriseHost $host, Carrier $carrier, string $recipient, string $message, int $messageID, $reply_with  )
    {
        $this->queue = 'outbound-throttled';
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
            return false;
        }
        else{
            //$this->queue = $this->from->e164;
        }
    }

    public function handle()
    {
        //This throttles 1 per second based on the sending DID!
        Redis::throttle( "throttle_" . substr( $this->from->e164, 2) )->allow(1)->every(1)->then(function ()
        {
            try{
                $thinq = new Guzzle([
                    'timeout' => 10.0,
                    'base_uri' => 'https://api.thinq.com',
                    'auth' => [ $this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)],
                    'headers' => [ 'content-type' => 'application/json' ],
                    'json' => [
                        'from_did' => substr( $this->from->e164, 2),
                        'to_did' => $this->recipient,
                        'message' => $this->message,
                    ],
                ]);
            }
            catch( Exception $e ){
                LogEvent::dispatch(
                    "Failed decrypting carrier api token",
                    get_class( $this ), 'error', json_encode($this->carrier->toArray()), null
                );
                return false;
            }

            $result = $thinq->post("account/{$this->carrier->thinq_account_id}/product/origination/sms/send");
            if( $result->getStatusCode() != 200 )
            {
                LogEvent::dispatch(
                    "Failure submitting message",
                    get_class( $this ), 'error', json_encode($result->getReasonPhrase()), null
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
                    get_class( $this ), 'error', json_encode($arr), null
                );
                return false;
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
                $arr['guid'],
                'outbound'
            );

            return true;
        }, function ()
        {
            return $this->release(10 );
        });
    }
}
