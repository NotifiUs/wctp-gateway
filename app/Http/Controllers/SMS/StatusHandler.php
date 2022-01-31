<?php

namespace App\Http\Controllers\SMS;

use Exception;
use App\Number;
use App\Carrier;
use App\Message;
use Carbon\Carbon;
use App\Jobs\LogEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Security\RequestValidator;

class StatusHandler extends Controller
{
    private $carrier;
    private $number;

    public function __invoke(Request $request, string $identifier)
    {
        $this->number = Number::where('enabled', 1 )->where('identifier', $identifier)->first();

        if( $this->number === null ){
            return $this->respond();
        }

        $this->carrier = Carrier::where('enabled',1)->where('id', $this->number->carrier_id )->first();

        if(  $this->carrier === null ){
            return $this->respond();
        }

        if( ! $this->verify($request) ) {
            return $this->respond();
        }

        if( $this->carrier->api == 'twilio' )
        {
            $carrier_uid = $request->input('MessageSid' );
        }
        elseif( $this->carrier->api == 'thinq')
        {
            $carrier_uid = $request->input('guid' );
        }
        else
        {
            return $this->respond();
        }

        $message = Message::where('carrier_message_uid', $carrier_uid )->first();
        if(  $message === null )
        {
            return $this->respond();
        }

        $message->status = $request->input('MessageStatus') ?? $request->input('send_status');
        switch ($message->status) {
            case "sent":
            case "DELIVRD":
            case "delivered":
                $message->delivered_at = Carbon::now();
                break;
            case "REJECTD":
            case "EXPIRED":
            case "DELETED":
            case "UNKNOWN":
            case "failed":
            case "undelivered":
            case "UNDELIV":
            default:
                $message->failed_at = Carbon::now();
                break;
        }

        $message->save(); //exception will cause carrier to retry, promising some level of consistency

        return $this->respond();
    }

    protected function verify( Request $request )
    {
        if( $this->carrier->api == 'twilio' )
        {
            try{
                $validator = new RequestValidator( decrypt( $this->carrier->twilio_auth_token )  );
            }
            catch( Exception $e )
            {
                LogEvent::dispatch(
                    "Failed inbound message",
                    get_class( $this ), 'error', json_encode("Unable to decrypt twilio auth token"), null
                );
            }

            if( $validator->validate(
                $request->header('X-Twilio-Signature'),
                $request->fullUrl(),
                $request->all()
            ))
            {
                return true;
            }

            LogEvent::dispatch(
                "Failed inbound message",
                get_class( $this ), 'error', json_encode("Unable to verify twilio request"), null
            );

            return false;

        }
        elseif( $this->carrier->api == 'thinq' )
        {
            //https://apidocs.thinq.com/?version=latest#7c5909e8-596c-47b3-9f24-438196eef374
            //comes from 192.81.236.250 //debating on leaving this up to network firewall?
            //move to system settings or carrier settings?
            if( $request->getClientIp() == '192.81.236.250')
            {
                return true;
            }

            LogEvent::dispatch(
                "Failed inbound message",
                get_class( $this ), 'error', json_encode("Request not from ThinQ documented IP address"), null
            );

            return false;
        }
        else
        {
            return false;
        }

    }

    protected function respond()
    {
        if( $this->carrier->api == 'thinq')
        {
            return response( json_encode([ 'success' => true ]), 200, ['content-type' => 'application/json']);
        }
        else
        {
            return response('<Response></Response>', 200, ['content-type' => 'application/xml']);
        }

    }
}
