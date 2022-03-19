<?php

namespace App\Jobs;

use Exception;
use Throwable;
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

class SendWebhookSMS implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;
    public int $timeout = 60;
    public int $uniqueFor = 3600;
    public bool $failOnTimeout = true;
    public bool $deleteWhenMissingModels = true;
    protected $host, $carrier, $recipient, $message, $messageID, $reply_with, $from;

    public function __construct( EnterpriseHost $host, Carrier $carrier, string $recipient, string $message, int|null $messageID, $reply_with  )
    {
        $this->onQueue('outbound');
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
        try{
            $webhook = new Guzzle([
                'timeout' => 10.0,
                'base_uri' => $this->carrier->webhook_host,
                'auth' => [ decrypt($this->carrier->webhook_username), decrypt($this->carrier->webhook_password)],
                'headers' => [ 'content-type' => 'application/json' ],
                'json' => [
                    'from' => $this->from->e164,
                    'to' => $this->recipient,
                    'message' => $this->message,
                    'id' => $this->messageID
                ]
            ]);
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failed decrypting carrier webhook password",
                get_class( $this ), 'error', json_encode($this->carrier->toArray()), null
            );
            $this->release(60 );
        }

        try{
            $result = $webhook->post($this->carrier->webhook_endpoint);
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure submitting webhook message",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );
            $this->release(60 );
        }

        if( $result->getStatusCode() != 200 )
        {
            LogEvent::dispatch(
                "Failure submitting webhook message",
                get_class( $this ), 'error', json_encode($result->getReasonPhrase()), null
            );
            $this->release(60 );
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
            'HTTP/' . $result->getReasonPhrase(),
            'outbound',
            'delivered'
        );

        return 0;
    }

    public function uniqueId()
    {
        return $this->messageID;
    }

    public function failed(Throwable $exception)
    {
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
            'webhook',
            'outbound',
            'failed'
        );
    }
}
