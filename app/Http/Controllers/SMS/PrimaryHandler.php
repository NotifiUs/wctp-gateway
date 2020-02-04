<?php

namespace App\Http\Controllers\SMS;

use Exception;
use App\Number;
use App\Carrier;
use Carbon\Carbon;
use App\Jobs\SaveMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrimaryHandler extends Controller
{
    private $carrier;
    private $number;

    public function __invoke(Request $request, string $identifier)
    {
        $this->number = Number::where('enabled', 1)->where('identifier', $identifier)->first();

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
            SaveMessage::dispatch(
                $this->carrier->id,
                $this->number->id,
                $request->input('To'),
                $request->input('From'),
                encrypt( $request->input('Body') ),
                null,
                Carbon::now(),
                null,
                $request->input('MessageSid'),
                'inbound'
            );
        }
        else
        {
            SaveMessage::dispatch(
                $this->carrier->id,
                $this->number->id,
                "+1{$request->input('to')}",
                "+1{$request->input('from')}",
                encrypt( $request->input('message') ),
                null,
                Carbon::now(),
                null,
                $request->header('X-sms-guid'),
                'inbound'
            );
        }


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
