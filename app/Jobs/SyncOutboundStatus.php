<?php

namespace App\Jobs;

use App\User;
use Throwable;
use Exception;
use App\Carrier;
use App\Message;
use Carbon\Carbon;
use App\Mail\FailedJob;
use Illuminate\Bus\Queueable;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncOutboundStatus implements ShouldQueue, ShouldBeUnique
{

    public int $tries = 10;
    public int $timeout = 60;
    public int $uniqueFor = 3600;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public Message|null $message;
    public Carrier|null $carrier;

    public function __construct( Message $message )
    {
        $this->message = $message;
    }

    public function handle()
    {
        $this->carrier = Carrier::where( 'enabled', 1 )->where( 'id', $this->message->carrier_id )->first();

        if( $this->carrier === null )
        {
            LogEvent::dispatch(
                "Failure synchronizing message status",
                get_class( $this ), 'error', json_encode("No enabled carrier for message"), null
            );

            $this->release(60);
        }

        if( $this->carrier->api == 'twilio' )
        {
            //use twilio api to check status of message->carrier_uniquie_id
            try{
                $client = new TwilioClient(
                    $this->carrier->twilio_account_sid,
                    decrypt( $this->carrier->twilio_auth_token )
                );
            }
            catch( Exception $e ){
                LogEvent::dispatch(
                    "Failure synchronizing message status",
                    get_class( $this ), 'error', json_encode($e->getMessage()), null
                );
                $this->release(60);
            }

            try{
                $carrier_message = $client->messages( $this->message->carrier_message_uid )->fetch();
                $this->message->status = $carrier_message->status;
                switch ($carrier_message->status) {
                    case "sent":
                    case "DELIVRD":
                    case "delivered":
                        $this->message->delivered_at = Carbon::now();
                        break;
                    case "REJECTD":
                    case "EXPIRED":
                    case "DELETED":
                    case "UNKNOWN":
                    case "failed":
                    case "undelivered":
                    case "UNDELIV":
                    default:
                        $this->message->failed_at = Carbon::now();
                        break;
                }
                $this->message->save();
            }
            catch( Exception $e ){
                LogEvent::dispatch(
                    "Failure synchronizing message status",
                    get_class( $this ), 'error', json_encode([$e->getMessage(), $this->from]), null
                );
                $this->release(60);
            }
        }
        elseif( $this->carrier->api == 'thinq')
        {
            /*
             * https://api.thinq.com/account/{{account_id}}/product/origination/sms/{{message_id}}
             */
            try{
                $thinq = new Guzzle([
                    'timeout' => 10.0,
                    'base_uri' => 'https://api.thinq.com',
                    'auth' => [ $this->carrier->thinq_api_username, decrypt($this->carrier->thinq_api_token)],
                ]);
            }
            catch( Exception $e ){
                LogEvent::dispatch(
                    "Failed decrypting carrier api token",
                    get_class( $this ), 'error', json_encode($this->carrier->toArray()), null
                );
                $this->release(60);
            }

            try{
                $result = $thinq->get("account/{$this->carrier->thinq_account_id}/product/origination/sms/{$this->message->carrier_message_uid}");
            }
            catch( Exception $e )
            {
                LogEvent::dispatch(
                    "Failure synchronizing message status",
                    get_class( $this ), 'error', json_encode($e->getMessage()), null
                );

                $this->release(60);
            }

            if( $result->getStatusCode() != 200 )
            {
                LogEvent::dispatch(
                    "Failure synchronizing message status",
                    get_class( $this ), 'error', json_encode($result->getReasonPhrase()), null
                );
                $this->release(60);
            }
            $body = $result->getBody();
            $json = $body->getContents();
            $arr = json_decode( $json, true );
            if( ! isset( $arr['delivery_notifications']))
            {
                LogEvent::dispatch(
                    "Failure getting delivery notifications",
                    get_class( $this ), 'error', json_encode([$arr, $arr['delivery_notifications']]), null
                );
                $this->release(60);
            }

            $ts = null;
            $latest_update = null;

            LogEvent::dispatch(
                "Delivery Notifications",
                get_class( $this ), 'info', json_encode([$arr['delivery_notifications']]), null
            );

            foreach( $arr['delivery_notifications'] as $dn )
            {
                if( $ts === null || Carbon::parse( $dn['timestamp'] ) >= $ts )
                {
                    $ts = Carbon::parse( $dn['timestamp']);
                    $latest_update = $dn;

                }
            }

            if(  $latest_update !== null )
            {
                switch( $latest_update['send_status'] ) {
                    case "sent":
                    case "DELIVRD":
                    case "delivered":
                        $this->message->status = $latest_update['send_status'];
                        $this->message->delivered_at = Carbon::parse($latest_update['timestamp']);
                        break;
                    case "REJECTD":
                    case "EXPIRED":
                    case "DELETED":
                    case "UNKNOWN":
                    case "failed":
                    case "undelivered":
                    case "UNDELIV":
                        $this->message->status = $latest_update['send_status'];
                        $this->message->failed_at = Carbon::parse($latest_update['timestamp']);
                        break;
                    default:
                        break;
                }
            }

            try{
                $this->message->save();
            }
            catch( Exception $e ){
                LogEvent::dispatch(
                    "Failure updating message status",
                    get_class( $this ), 'error', json_encode($e->getMessage()), null
                );
                $this->release(60);
            }
        }

        return;
    }

    public function uniqueId()
    {
        return $this->message->id;
    }

    public function failed(Throwable $exception )
    {

        foreach (User::where('email_notifications', true)->get() as $u) {
            try{
                Mail::to($u->email)->send(new FailedJob($this->message->toArray()));
            }
            catch( Exception $e )
            {
                LogEvent::dispatch(
                    "Unable to send failed jobs notifications",
                    get_class($this), 'error', json_encode([$e->getMessage(), $u->email]), null
                );
            }
        }

        LogEvent::dispatch(
            "SyncOutboundStatus Job failed",
            get_class($this), 'error', json_encode($exception->getMessage()), null
        );

        try {
            $this->message->status = 'failed';
            $this->message->failed_at = Carbon::now();
            $this->message->save();
        } catch (Exception $e) {
            LogEvent::dispatch(
                "Job status update failed",
                get_class($this), 'error', json_encode($e->getMessage()), null
            );
        }
    }
}
