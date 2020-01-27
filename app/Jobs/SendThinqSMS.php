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
                'base_uri' => 'https://api.thinq.com',
                'auth' => [ $this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)],
                'headers' => [ 'content-type' => 'application/json' ],
                'json' => [
                    'from_did' => $this->carrier->numbers()->first()->e164,
                    'to_did' => $this->recipient,
                    'message' => $this->message,
                ],
            ]);
        }
        catch( Exception $e ){ return false; }

        return true;
    }
}
