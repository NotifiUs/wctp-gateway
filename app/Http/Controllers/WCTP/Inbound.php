<?php

namespace App\Http\Controllers\WCTP;

use Exception;
use App\Carrier;
use SimpleXMLElement;
use App\Jobs\LogEvent;
use App\EnterpriseHost;
use App\Jobs\SendThinqSMS;
use App\Jobs\SendTwilioSMS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class Inbound extends Controller
{
    public function __invoke(Request $request)
    {
        $wctp = new SimpleXMLElement( $request->getContent() );

        $recipient = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Recipient/@recipientID')[0];
        $message = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-Payload/wctp-Alphanumeric')[0];

        $senderID = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Originator/@senderID')[0];
        $securityCode = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Originator/@securityCode')[0];

        $paramCheck = $this->checkParams([
            'recipient' => $recipient,
            'message' => $message,
            'senderID' => $senderID,
            'securityCode' => $securityCode,
        ]);

        if( ! $paramCheck['success'] )
        {
            return $this->showError( $paramCheck['errorCode'], $paramCheck['errorText'],
                $paramCheck['errorDesc']);
        }

        $host = EnterpriseHost::where('senderID', $senderID )->first();

        if( is_null( $host ) )
        {
            return $this->showError( 401, 'Invalid senderID',
                'senderID does not live on this system');
        }

        try{
            if( $securityCode != decrypt( $host->securityCode ) )
            {
                return $this->showError( 402, 'Invalid securityCode',
                    'Unable to decrypt securityCode');
            }
        }
        catch( Exception $e ){
            return $this->showError( 402, 'Invalid securityCode',
                'Unable to decrypt securityCode');
        }

        $carrier = Carrier::where('enabled', 1)->orderBy('priority')->first();
        if( is_null($carrier)){
            return $this->showError( 606, 'Service Unavailable',
                'No upstream carriers are enabled');
        }

        if( $carrier->api == 'twilio' )
        {
            SendTwilioSMS::dispatch( $host, $carrier, $recipient, $message );

        }
        elseif( $carrier->api == 'thinq' )
        {
            SendThinqSMS::dispatch( $host, $carrier, $recipient, $message );
        }
        else
        {
            return $this->showError( 604, 'Internal Server Error',
                'This carrier API is not yet implemented');
        }

        return view('WCTP.wctp-Confirmation')
            ->with('successCode', '200' )
            ->with('successText', 'Message queued for delivery' );
    }

    private function showError( $code, $text, $desc )
    {
        LogEvent::dispatch(
            "Failed WCTP connection",
            get_class( $this ), 'info', json_encode([$code, $text, $desc]), null
        );

        return view('WCTP.wctp-Failure')
            ->with('errorCode', $code )
            ->with('errorText', $text )
            ->with('errorDesc', $desc );
    }

    private function checkParams( array $data )
    {
        $validator = Validator::make([
            'recipient' => $data['recipient'],
        ],[
            'recipient' => 'required|string|size:10',
        ]);

        if( $validator->fails() )
        {
            return [
                'success' => false,
                'errorCode' => 403,
                'errorText' => 'Invalid recipientID',
                'errorDesc' => 'The recipientID is invalid',
            ];
        }

        $validator = Validator::make([
            'message' => $data['message'],
        ],[
            'message' => 'required|string|max:1600',
        ]);

        if( $validator->fails() )
        {
            return [
                'success' => false,
                'errorCode' => 411,
                'errorText' => 'Message exceeds allowable length',
                'errorDesc' => 'Message exceeds allowable message length of 1600',
            ];
        }

        $validator = Validator::make([
            'senderID' => $data['senderID'],
        ],[
            'senderID' => 'required|string|max:128',
        ]);

        if( $validator->fails() )
        {
            return [
                'success' => false,
                'errorCode' => 401,
                'errorText' => 'Invalid senderID',
                'errorDesc' => 'The senderID is invalid',
            ];
        }

        $validator = Validator::make([
            'securityCode' => $data['securityCode'],
        ],[
            'securityCode' => 'required|string|max:16',
        ]);

        if( $validator->fails() )
        {
            return [
                'success' => false,
                'errorCode' => 402,
                'errorText' => 'Invalid security code',
                'errorDesc' => 'The security code for this senderID is invalid',
            ];
        }

        return [
            'success' => true,
            'errorCode' => 0,
            'errorText' => '',
            'errorDesc' => '',
        ];
    }
}
