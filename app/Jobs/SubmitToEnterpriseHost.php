<?php

namespace App\Jobs;

use App\User;
use Illuminate\Queue\MaxAttemptsExceededException;
use Throwable;
use Exception;
use App\Message;
use Carbon\Carbon;
use App\Mail\RetryJob;
use App\Mail\FailedJob;
use App\EnterpriseHost;
use Illuminate\Bus\Queueable;
use GuzzleHttp\Client as Guzzle;
use NotifiUs\WCTP\XML\MessageReply;
use NotifiUs\WCTP\XML\SubmitRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SubmitToEnterpriseHost implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public $message;

    public $tries = 10;
    public $timeout = 60;
    public $uniqueFor = 3600;

    public function __construct( Message $message )
    {
        $this->queue = 'enterprise-host';
        $this->message = $message;
    }

    public function handle()
    {

        if( $this->attempts() === 2 )
        {
            Mail::to( User::first()->email )->send(new RetryJob($this->message->toArray() ));
        }

        $host = EnterpriseHost::where( 'enabled', 1 )->where( 'id', $this->message->enterprise_host_id )->first();

        if( is_null( $host ) )
        {
            LogEvent::dispatch(
                "Failure submitting reply",
                get_class( $this ), 'error', json_encode("No host found"), null
            );

            $this->release(60 );
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
                $this->release(60 );
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
                $this->release(60 );
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
            $this->release(60 );
        }

        if( $result->getStatusCode() != 200 )
        {
            LogEvent::dispatch(
                "Failure submitting message",
                get_class( $this ), 'error', json_encode([nl2br($result->getBody()->getContents()), $result->getReasonPhrase() ]), null
            );
            $this->release(60 );
        }

        //verify wctpresponse
        $body = simplexml_load_string( $result->getBody()->getContents() );

        if($body === false)
        {
            LogEvent::dispatch(
                "Response was not XML.",
                get_class( $this ), 'error', json_encode( $body ), null
            );
            $this->release(60 );
        }
        else
        {
            try{
                $wctpConfirmation = (string)$body->xpath('/wctp-Operation/wctp-Confirmation/wctp-Success/@successCode')[0] ?? null;
            }
            catch(Exception $e )
            {
                LogEvent::dispatch(
                    "No wctp-Confirmation operation response",
                    get_class( $this ), 'error', json_encode('No successCode found: ' . $e->getMessage() ), null
                );
                $this->release(60 );
            }

            if(is_null($wctpConfirmation))
            {
                LogEvent::dispatch(
                    "Missing successCode on wctpSuccess element",
                    get_class( $this ), 'error', json_encode($body ), null
                );
                $this->release(60 );
            }
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
            $this->release(60 );
        }


        LogEvent::dispatch(
            "Enterprise Host response",
            get_class( $this ), 'info', json_encode($body), null
        );

        return;
    }

    public function uniqueId()
    {
        return $this->message->id;
    }

    public function failed(Throwable $exception )
    {
        if( $exception instanceof MaxAttemptsExceededException )
        {
            // instead of delaying the job instance, we just let it silently fail
            // the scheduled job will pick it up when it runs next
            //SubmitToEnterpriseHost::dispatch($this->message)->delay(now()->addSeconds(mt_rand(10,90)));
        }
        else
        {
            //move to system setting eventually.
            Mail::to( User::first()->email )->send(new FailedJob($this->message->toArray() ));

            try{
                $this->message->status = 'failed';
                $this->message->failed_at = Carbon::now();
                $this->message->save();
            }
            catch( Exception $e ){
                LogEvent::dispatch(
                    "Job status update failed",
                    get_class( $this ), 'error', json_encode($e->getMessage()), null
                );
            }
        }

    }

}
