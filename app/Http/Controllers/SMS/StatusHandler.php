<?php

namespace App\Http\Controllers\SMS;

use Carbon\Carbon;
use Exception;
use App\Number;
use App\Carrier;
use App\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatusHandler extends Controller
{
    private $carrier;
    private $number;

    public function __invoke(Request $request, string $identifier)
    {
        $this->number = Number::where('enabled', 1 )->where('identifier', $identifier)->first();

        if( is_null( $this->number ) ){
            return $this->respond();
        }

        $this->carrier = Carrier::where('enabled',1)->where('id', $this->number->carrier_id )->first();

        if( is_null( $this->carrier ) ){
            return $this->respond();
        }

        if( ! $this->verify() ) {
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
        if( is_null( $message) )
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
                $message->failed_at = Carbon::now();
                break;
            default:
                break;
        }

        $message->save(); //exception will cause carrier to retry, promising some level of consistency

        return $this->respond();
    }

    protected function verify()
    {
        if( $this->carrier->api == 'twilio' )
        {
            //use verification
        }
        elseif( $this->carrier->api == 'thinq' )
        {
            //useragent == 'thinq-sms'
            //X-sms-guid is set
        }
        else
        {
            return false;
        }

        return true;
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
