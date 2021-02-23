<?php

namespace App\Jobs;

use Exception;
use App\Message;
use Carbon\Carbon;
use App\EnterpriseHost;
use Illuminate\Bus\Queueable;
use GuzzleHttp\Client as Guzzle;
use NotifiUs\WCTP\XML\MessageReply;
use NotifiUs\WCTP\XML\SubmitRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SubmitToEnterpriseHost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public $message;

    public $tries = 10;
    public $timeout = 60;

    public function __construct( Message $message )
    {
        $this->queue = 'enterprise-host';
        $this->message = $message;
    }

    public function handle()
    {
        $host = EnterpriseHost::where( 'enabled', 1 )->where( 'id', $this->message->enterprise_host_id )->first();

        if( is_null( $host ) )
        {
            LogEvent::dispatch(
                "Failure submitting reply",
                get_class( $this ), 'error', json_encode("No host found"), null
            );
            return false;
        }

        $responding_to = null;

        if( $this->message->reply_with )
        {
            $responding_to = Message::where('reply_with', $this->message->reply_with )
                ->where('direction','outbound')
                ->where('to', $this->message->from )
                ->where('from', $this->message->to )
                ->where('created_at', '>=', Carbon::now()->subHours(4 ) )
                ->first();
        }

        if( ! is_null($responding_to))
        {
            $messageReply = new MessageReply();
            try{
                $xml = $messageReply
                    ->responseToMessageID( $responding_to->messageID )
                    ->submitTimestamp( Carbon::now() )
                    ->senderID( substr( $this->message->from, 2) )
                    ->recipientID( substr($this->message->to, 2) )
                    ->messageID( $this->message->id )
                    ->payload( decrypt($this->message->message ) )
                    ->xml();
            }
            catch( Exception $e ){
                LogEvent::dispatch(
                    "Failure creating MessageReply",
                    get_class( $this ), 'error', json_encode($e->getMessage()), null
                );
                return false;
            }
        }
        else
        {
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
        }

        $guzzleConfig = [
            'http_errors' => false, //don't throw exception
            'timeout' => 10.0,
            'headers' => [ 'content-type' => 'application/xml' ],
            'body' => $xml->asXML(),
            'verify' => config('tls.verify_certificates'),
            'curl' => [ CURLOPT_SSLVERSION => constant( config('tls.protocol_support' ) ) ],
        ];

        $enterpriseHost = new Guzzle( $guzzleConfig );

        try{
            $result = $enterpriseHost->post( $host->url );
        }
        catch( Exception $e )
        {
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'error', json_encode($e->getMessage() ), null
            );
            return false;
        }

        if( $result->getStatusCode() != 200 )
        {
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'error', json_encode([nl2br($result->getBody()->getContents()), $result->getReasonPhrase() ]), null
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

        $body = simplexml_load_string( $result->getBody()->getContents() );
        LogEvent::dispatch(
            "Enterprise Host response",
            get_class( $this ), 'info', json_encode($body), null
        );

        return true;
    }
}
