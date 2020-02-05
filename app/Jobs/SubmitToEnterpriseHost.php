<?php

namespace App\Jobs;

use Exception;
use App\Number;
use App\Carrier;
use App\Version;
use App\Message;
use Carbon\Carbon;
use App\EnterpriseHost;
use Illuminate\Bus\Queueable;
use GuzzleHttp\Client as Guzzle;
use NotifiUs\WCTP\XML\SubmitRequest;
//use NotifiUs\WCTP\XML\MessageReply;
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
            LogEvent::dispatch(
                "Failure submitting reply",
                get_class( $this ), 'error', json_encode("No host found"), null
            );
            return false;
        }

        $submitRequest = new SubmitRequest( );

        try{
            $xml = $submitRequest
                ->submitTimestamp( Carbon::now() )
                ->senderID( substr( $this->message->from, 2) )
                ->recipientID( substr($this->message->to, 2) )
                ->messageID( $this->message->messageID ?? $this->message->id )
                ->payload( decrypt($this->message->message ) )
                ->xml();
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure creating SubmitRequest",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );
            return false;
        }

        $enterpriseHost = new Guzzle([
            'timeout' => 10.0,
            'headers' => [ 'content-type' => 'application/xml' ],
            'body' => $xml->asXML(),
        ]);

        $result = $enterpriseHost->post( $host->url );

        if( $result->getStatusCode() != 200 )
        {
            LogEvent::dispatch(
                "Failure submitting reply",
                get_class( $this ), 'error', json_encode($result->getReasonPhrase()), null
            );
            return false;
        }

        try{
            $this->message->status = 'delivered';
            $this->message->delivered_at = Carbon::now();
            $this->message->save();
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure updating status",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );
        }

        $body = $result->getBody();
        LogEvent::dispatch(
            "Enterprise Host response",
            get_class( $this ), 'info', json_encode($body), null
        );

        return true;
    }
}
