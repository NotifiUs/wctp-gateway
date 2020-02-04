<?php

namespace App\Http\Controllers\SMS;

use Exception;
use App\Number;
use App\Carrier;
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

        $this->carrier = Carrier::Where('enabled',1)->where('id', $this->number->carrier_id )->first();

        if( is_null( $this->carrier ) ){
            return $this->respond();
        }

        if( ! $this->verify() ) {
            return $this->respond();
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
