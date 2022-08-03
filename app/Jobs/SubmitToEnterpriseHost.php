<?php

namespace App\Jobs;

use App\Mail\FailedJob;
use App\Mail\RetryJob;
use App\Models\EnterpriseHost;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use NotifiUs\WCTP\XML\MessageReply;
use NotifiUs\WCTP\XML\SubmitRequest;
use Throwable;

class SubmitToEnterpriseHost implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;

    public int $tries = 10;

    public int $timeout = 60;

    public int $uniqueFor = 3600;

    public bool $failOnTimeout = true;

    public bool $deleteWhenMissingModels = true;

    public function __construct(Message $message)
    {
        $this->onQueue('enterprise-host');
        $this->message = $message;
    }

    public function handle()
    {
        if ($this->attempts() === 2) {
            foreach (User::where('email_notifications', true)->get() as $u) {
                Mail::to($u->email)->send(new RetryJob($this->message->toArray()));
            }
        }

        $host = EnterpriseHost::where('enabled', 1)->where('id', $this->message->enterprise_host_id)->first();

        if ($host === null) {
            LogEvent::dispatch(
                'Failure submitting reply',
                get_class($this), 'error', json_encode('No host found'), null
            );

            $this->release(60);
        }

        $responding_to = null;

        $msg_from = Str::startsWith($this->message->from, '+1') ? substr($this->message->from, 2) : $this->message->from;
        $msg_to = Str::startsWith($this->message->to, '+1') ? substr($this->message->to, 2) : $this->message->to;

        if ($this->message->reply_with) {
            $responding_to = Message::where('reply_with', $this->message->reply_with)
                ->where('direction', 'outbound')
                ->where('to', $msg_from)
                ->where('from', $msg_to)
                ->where('created_at', '>=', Carbon::now()->subHours(4))
                ->first();
        }

        if ($responding_to !== null) {
            $messageReply = new MessageReply();
            try {
                $xml = $messageReply
                    ->responseToMessageID($responding_to->messageID)
                    ->submitTimestamp(Carbon::now())
                    ->senderID($msg_from)
                    ->recipientID($msg_to)
                    ->messageID($this->message->id)
                    ->payload(decrypt($this->message->message) ?? '')
                    ->xml();
            } catch (Exception $e) {
                LogEvent::dispatch(
                    'Failure creating MessageReply',
                    get_class($this), 'error', json_encode($e->getMessage()), null
                );
                $this->fail($e);
            }
        } else {
            $submitRequest = new SubmitRequest();
            try {
                $xml = $submitRequest
                    ->submitTimestamp(Carbon::now())
                    ->senderID($msg_from)
                    ->recipientID($msg_to)
                    ->messageID($this->message->messageID ?? $this->message->id)
                    ->payload(decrypt($this->message->message) ?? '')
                    ->xml();
            } catch (Exception $e) {
                LogEvent::dispatch(
                    'Failure creating SubmitRequest',
                    get_class($this), 'error', json_encode($e->getMessage()), null
                );
                $this->fail($e);
            }
        }

        $guzzleConfig = [
            'timeout' => 30.0,
            'headers' => ['content-type' => 'application/xml'],
            'body' => $xml->asXML(),
            'verify' => config('tls.verify_certificates'),
            'curl' => [
                CURLOPT_SSLVERSION => constant(config('tls.protocol_support')),
                CURLOPT_SSL_CIPHER_LIST => config('tls.cipher_list')
            ],
        ];

        $enterpriseHost = new Guzzle($guzzleConfig);

        try {
            $result = $enterpriseHost->post($host->url);
        } catch (Exception $e) {
            LogEvent::dispatch(
                'Failure submitting message',
                get_class($this), 'error', json_encode($e->getMessage()), null
            );
            $this->release(60);
        }

        if ($result->getStatusCode() !== 200) {
            LogEvent::dispatch(
                'Failure submitting message',
                get_class($this), 'error', json_encode([nl2br($result->getBody()->getContents()), $result->getReasonPhrase()]), null
            );
            $this->release(60);
        }

        //verify wctpresponse
        $body = simplexml_load_string($result->getBody()->getContents());

        if ($body === false) {
            LogEvent::dispatch(
                'Response was not XML.',
                get_class($this), 'error', json_encode(['body' => $body,
                    'results' => [
                        'status_code' => $result->getStatusCode(),
                        'headers' => $result->getHeaders(),
                    ],
                ]), null
            );
            $this->release(60);
        } else {
            try {
                $wctpConfirmation = (string) $body->xpath('/wctp-Operation/wctp-Confirmation/wctp-Success/@successCode')[0] ?? null;
            } catch (Exception $e) {
                LogEvent::dispatch(
                    'No wctp-Confirmation operation response',
                    get_class($this), 'error', json_encode('No successCode found: '.$e->getMessage()), null
                );
                $this->release(60);
            }

            if ($wctpConfirmation === null) {
                LogEvent::dispatch(
                    'Missing successCode on wctpSuccess element',
                    get_class($this), 'error', json_encode($body), null
                );
                $this->release(60);
            }
        }

        try {
            $this->message->status = 'delivered';
            $this->message->delivered_at = Carbon::now();
            $this->message->save();
        } catch (Exception $e) {
            LogEvent::dispatch(
                'Failure updating status',
                get_class($this), 'error', json_encode($e->getMessage()), null
            );
            $this->release(60);
        }

        LogEvent::dispatch(
            'Enterprise Host response',
            get_class($this), 'info', json_encode($body), null
        );

        return 0;
    }

    public function uniqueId()
    {
        return $this->message->id;
    }

    public function failed(Throwable $exception)
    {
        if ($exception instanceof MaxAttemptsExceededException) {
            // instead of delaying the job instance, we just let it silently fail
            // the scheduled job will pick it up when it runs next
            //SubmitToEnterpriseHost::dispatch($this->message)->delay(now()->addSeconds(mt_rand(10,90)));
        } else {
            foreach (User::where('email_notifications', true)->get() as $u) {
                Mail::to($u->email)->send(new FailedJob($this->message->toArray()));
            }

            try {
                $this->message->status = 'failed';
                $this->message->failed_at = Carbon::now();
                $this->message->save();
            } catch (Exception $e) {
                LogEvent::dispatch(
                    'Job status update failed',
                    get_class($this), 'error', json_encode($e->getMessage()), null
                );
            }
        }
    }
}
