<?php

namespace App\Jobs;

use Carbon\Carbon;
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
            $from = $this->carrier->numbers()->inRandomOrder()->where('enabled', 1)->first();
            $thinq = new Guzzle([
                'timeout' => 10.0,
                'base_uri' => 'https://api.thinq.com',
                'auth' => [ $this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)],
                'headers' => [ 'content-type' => 'application/json' ],
                'json' => [
                    'from_did' => substr( $from->e164, 2),
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
            $from->id,
            "+1{$this->recipient}",
            $from->e164,
            encrypt( $this->message ),
            $this->messageID,
            Carbon::now(),
            $this->reply_with
        );

        return true;
    }
}
