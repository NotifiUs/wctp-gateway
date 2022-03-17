<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use App\Models\Carrier;
use Illuminate\Bus\Queueable;
use App\Models\EnterpriseHost;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendSunwireSMS implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;
    public int $timeout = 60;
    public int $uniqueFor = 3600;
    public bool $deleteWhenMissingModels = true;
    protected $host, $carrier, $recipient, $message, $messageID, $reply_with, $from;

    public function __construct( EnterpriseHost $host, Carrier $carrier, string $recipient, string $message, int|null $messageID, $reply_with  )
    {
        $this->queue = 'outbound';
        $this->host = $host;
        $this->carrier = $carrier;
        $this->recipient = $recipient;
        $this->message = $message;
        $this->messageID = $messageID;
        $this->reply_with = $reply_with;
        $this->from = $this->carrier->numbers()->inRandomOrder()->where('enabled', 1)->where('enterprise_host_id', $this->host->id )->first();
        if( $this->from === null )
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
        //API URLs: https://mars2.sunwire.ca/sms/ or https://mars1.sunwire.ca/sms/
        try{
            $json_array = [
                'From' => substr( $this->from->e164, 2),
                'To' => $this->recipient,
                'Body' => $this->message,
                'Receipt' => 'yes'
            ];

            $shared_config = [
                'timeout' => 10.0,
                'headers' => [ 'content-type' => 'application/json' ],
                'json' => $json_array,
            ];

            $sunwire1 = new Guzzle(array_merge($shared_config, ['base_uri' => 'https://mars1.sunwire.ca']));
            $sunwire2 = new Guzzle(array_merge($shared_config, ['base_uri' => 'https://mars2.sunwire.ca']));
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failed creating Sunwire JSON request",
                get_class( $this ), 'error', json_encode($this->carrier->toArray()), null
            );
            $this->release(60 );
        }

        //Try the primary mars1, and if it fails, try mars2
        //then finally mark as failed if mars2 fails
        try{
            $result = $sunwire1->post('/sms');
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Sunwire mars1 host failed, trying mars2",
                get_class( $this ), 'info', json_encode($e->getMessage()), null
            );

            try{
                $result = $sunwire2->post('/sms');
            }
            catch( Exception $e2 ){
                LogEvent::dispatch(
                    "Sunwire mars2 host failed",
                    get_class( $this ), 'info', json_encode($e2->getMessage()), null
                );
                $this->release(60 );
            }
        }

        if( $result->getStatusCode() != 200 )
        {
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'error', json_encode($result->getReasonPhrase()), null
            );
            $this->release(60 );
        }
        $body = $result->getBody();
        $json = $body->getContents();
        $arr = json_decode( $json, true );
        if( ! isset( $arr['Status']))
        {
            LogEvent::dispatch(
                "Improper JSON response received from Sunwire",
                get_class( $this ), 'error', json_encode($arr), null
            );
            $this->release(60 );
        }

        if($arr['Status'] !== 'OK')
        {
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'error', json_encode(['response' => $arr, 'reason' => $result->getReasonPhrase()]), null
            );
            $this->release(60);
        }

        SaveMessage::dispatch(
            $this->carrier->id,
            $this->from->id,
            $this->host->id,
            $this->recipient,
            $this->from->e164,
            encrypt( $this->message ),
            $this->messageID,
            Carbon::now(),
            $this->reply_with,
            $arr['ID'],
            'outbound'
        );
        return 0;
    }

    public function uniqueId()
    {
        return $this->messageID;
    }
}