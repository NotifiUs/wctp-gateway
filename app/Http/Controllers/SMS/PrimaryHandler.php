<?php

namespace App\Http\Controllers\SMS;

use Exception;
use App\Number;
use App\Carrier;
use Carbon\Carbon;
use App\EnterpriseHost;
use App\Jobs\SaveMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrimaryHandler extends Controller
{
    private $carrier;
    private $number;
    private $host;

    public function __invoke(Request $request, string $identifier)
    {
        $this->number = Number::where('enabled', 1)->where('identifier', $identifier)->first();

        if( is_null( $this->number ) ){
            return $this->respond();
        }

        $this->host = EnterpriseHost::where('enabled', 1)->where('id', $this->number->enterprise_host_id )->first();
        if( is_null( $this->host ) )
        {
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
            $reply_phrase = preg_match('/\b\d+( ?ok(ay)?)\b/i', $request->input('Body'), $matches );
            if( $reply_phrase && isset($matches[0]) )
            {
                $reply_with = str_replace(['ok','okay'], '', $matches[0]);
            }
            else{ $reply_with = null; }

            SaveMessage::dispatch(
                $this->carrier->id,
                $this->number->id,
                $this->host->id,
                $request->input('To'),
                $request->input('From'),
                encrypt( $request->input('Body') ),
                null,
                Carbon::now(),
                $reply_with,
                $request->input('MessageSid'),
                'inbound'
            );

        }
        else
        {
            $reply_phrase = preg_match('/\b\d+( ?ok(ay)?)\b/i', $request->input('message'), $matches );
            if( $reply_phrase && isset($matches[0]) )
            {
                $reply_with = trim( str_replace(['ok','okay'], '', $matches[0]));
            }
            else{ $reply_with = null; }

            SaveMessage::dispatch(
                $this->carrier->id,
                $this->number->id,
                $this->host->id,
                "+1{$request->input('to')}",
                "+1{$request->input('from')}",
                encrypt( $request->input('message') ),
                null,
                Carbon::now(),
                $reply_with,
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
