<?php

namespace App\Jobs;

use Exception;
use App\Number;
use App\Carrier;
use App\Message;
use Carbon\Carbon;
use App\EnterpriseHost;
use Illuminate\Bus\Queueable;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SubmitToEnterpriseHost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public $message;

    public function __construct( Message $message )
    {
        $this->message = $message;
    }

    public function handle()
    {
        $host = EnterpriseHost::where( 'enabled', 1 )->where( 'id', $this->message->enterprise_host_id )->first();
        if( is_null( $host ) )
        {
            //no host to submit the message to
            return false;
        }

        $enterpriseHost = new Guzzle([
            'timeout' => 10.0,
            'base_uri' => $host->url,
            'headers' => [ 'content-type' => 'application/xml' ],
            'data' => '',
        ]);


        $result = $enterpriseHost->post();

        if( $result->getStatusCode() != 200 )
        {
            LogEvent::dispatch(
                "Failure submitting reply",
                get_class( $this ), 'error', json_encode($result->getReasonPhrase()), null
            );
            return false;
        }

        $body = $result->getBody();

        return true;
    }
}
