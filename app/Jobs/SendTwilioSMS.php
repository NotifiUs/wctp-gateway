<?php

namespace App\Jobs;

use App\Models\Carrier;
use App\Models\EnterpriseHost;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Throwable;
use Twilio\Rest\Client as TwilioClient;

class SendTwilioSMS implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;

    public int $timeout = 60;

    protected string $number;

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

    public function __construct(EnterpriseHost $host, Carrier $carrier, string $recipient, string $message, int|null $messageID, $reply_with)
    {
        $this->onQueue('outbound');
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
        try {
            $client = new TwilioClient(
                $this->carrier->twilio_account_sid,
                decrypt($this->carrier->twilio_auth_token)
            );
        } catch (Exception $e) {
            LogEvent::dispatch(
                'Failure submitting message',
                get_class($this), 'error', json_encode($e->getMessage()), null
            );
            $this->release(60);
        }

        try {
            if ($this->from === null) {
                LogEvent::dispatch(
                    'Failure submitting message',
                    get_class($this), 'error', json_encode('No enabled numbers assigned to host'), null
                );
                $this->release(60);
            }

            $params = [
                'messagingServiceSid' => $this->from->identifier,
                'body' => $this->message,
            ];

            if ($this->from->getType() == 'PN') {
                $params = [
                    'from' => $this->from->e164,
                    'body' => $this->message,
                ];
            }

            if (Str::startsWith($this->recipient, '+')) {
                //the + symbol is the exit code, so we assume the remaining is country code + number
                $this->number = $this->recipient;
            } elseif (Str::length($this->recipient) === 11 && Str::startsWith($this->recipient, '1')) {
                //11 digit number with no + and 1 as the country code
                //i.e. NANP area codes
                $this->number = "+{$this->recipient}";
            } elseif (Str::length($this->recipient) === 10) {
                //assume 10 digits are NANP
                $this->number = "+1{$this->recipient}";
            } else {
                $this->number = "+{$this->recipient}";
            }

            $msg = $client->messages->create(
                $this->number,
                $params
            );
        } catch (Exception $e) {
            LogEvent::dispatch(
                'Failure sending message',
                get_class($this), 'error', json_encode([$e->getMessage(), $this->from]), null
            );
            $this->release(60);
        }

        SaveMessage::dispatch(
            $this->carrier->id,
            $this->from->id,
            $this->host->id,
            $this->number,
            $this->from->e164,
            encrypt($this->message),
            $this->messageID,
            Carbon::now(),
            $this->reply_with,
            $msg->sid,
            'outbound'
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
            $this->number ?? 'unknown',
            $this->from->e164,
            encrypt($this->message),
            $this->messageID,
            Carbon::now(),
            $this->reply_with,
            'twilio',
            'outbound',
            'failed'
        );
    }
}
