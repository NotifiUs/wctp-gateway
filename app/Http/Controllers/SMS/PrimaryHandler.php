<?php

namespace App\Http\Controllers\SMS;

use Exception;
use App\Number;
use App\Carrier;
use Carbon\Carbon;
use App\Jobs\LogEvent;
use App\EnterpriseHost;
use App\Jobs\SaveMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Security\RequestValidator;

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

        if( ! $this->verify($request) ) {
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

                return false;
            }

            if( $validator->validate(
                $request->header('x-twilio-signature' ),
                $request->url(),
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
