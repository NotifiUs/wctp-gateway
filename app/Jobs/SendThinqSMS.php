<?php

namespace App\Jobs;

use App\Models\Carrier;
use App\Models\EnterpriseHost;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Throwable;

class SendThinqSMS implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;

    public int $timeout = 60;

    public int $uniqueFor = 3600;

    public bool $failOnTimeout = true;

    public bool $deleteWhenMissingModels = true;

    protected $host;

    protected $carrier;

    protected $recipient;

    protected $message;

    protected $messageID;

    protected $reply_with;

    protected $from;

    public function __construct(EnterpriseHost $host, Carrier $carrier, string $recipient, string $message, int $messageID, $reply_with)
    {
        $this->onQueue('outbound-throttled');
        $this->host = $host;
        $this->carrier = $carrier;
        $this->recipient = $recipient;
        $this->message = $message;
        $this->messageID = $messageID;
        $this->reply_with = $reply_with;
        $this->from = $this->carrier->numbers()->inRandomOrder()->where('enabled', 1)->where('enterprise_host_id', $this->host->id)->first();

        if ($this->from === null) {
            LogEvent::dispatch(
                'Failure submitting message',
                get_class($this), 'error', json_encode('No enabled numbers assigned to host'), null
            );
            $this->release(60);
        }
    }

    public function handle()
    {
        //This throttles 1 per second based on the sending DID!
        Redis::throttle('throttle_'.substr($this->from->e164, 2))->allow(1)->every(1)->then(function () {
            try {
                $thinq = new Guzzle([
                    'timeout' => 10.0,
                    'base_uri' => 'https://api.thinq.com',
                    'auth' => [$this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)],
                    'headers' => ['content-type' => 'application/json'],
                    'json' => [
                        'from_did' => substr($this->from->e164, 2),
                        'to_did' => $this->recipient,
                        'message' => $this->message,
                    ],
                ]);
            } catch (Exception $e) {
                LogEvent::dispatch(
                    'Failed decrypting carrier api token',
                    get_class($this), 'error', json_encode($this->carrier->toArray()), null
                );
                $this->release(60);
            }

            try {
                $result = $thinq->post("account/{$this->carrier->thinq_account_id}/product/origination/sms/send");
            } catch (Exception $e) {
                LogEvent::dispatch(
                    'No message GUID returned from carrier',
                    get_class($this), 'error', json_encode($e->getMessage()), null
                );
                $this->release(60);
            }

            if ($result->getStatusCode() != 200) {
                LogEvent::dispatch(
                    'Failure submitting message',
                    get_class($this), 'error', json_encode($result->getReasonPhrase()), null
                );
                $this->release(60);
            }
            $body = $result->getBody();
            $json = $body->getContents();
            $arr = json_decode($json, true);
            if (! isset($arr['guid'])) {
                LogEvent::dispatch(
                    'No message GUID returned from carrier',
                    get_class($this), 'error', json_encode($arr), null
                );
                $this->release(60);
            }

            SaveMessage::dispatch(
                $this->carrier->id,
                $this->from->id,
                $this->host->id,
                "+1{$this->recipient}",
                $this->from->e164,
                encrypt($this->message),
                $this->messageID,
                Carbon::now(),
                $this->reply_with,
                $arr['guid'],
                'outbound'
            );

            return 0;
        }, function () {
            // Could not obtain lock...try again after 1 second (1msg per second rate limit)
            $this->release(1);
        });
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
            "+1{$this->recipient}",
            $this->from->e164,
            encrypt($this->message),
            $this->messageID,
            Carbon::now(),
            $this->reply_with,
            'thinq',
            'outbound',
            'failed'
        );
    }
}
