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

        if( ! $wctp->valid() )
        {
            return $this->showError(301, 'Cannot Parse Input',
               'Message body is not XML');
        }

        $recipient = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Recipient/@recipientID')[0] ?? null;
        $message = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-Payload/wctp-Alphanumeric')[0] ?? null;
        $messageID = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-MessageControl/@messageID')[0] ?? null;
        $senderID = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Originator/@senderID')[0] ?? null;
        $securityCode = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Originator/@securityCode')[0] ?? null;

        if( is_null($recipient) || is_null($message) || is_null($messageID) || is_null($senderID) || is_null($securityCode) )
        {
            return $this->showError(302, 'XML Validation Error',
                'recipientID, wctp-Alphanumeric, MessageID, senderID, and securityCode are required.');
        }

        $reply_with = null;
        $reply_phrase = preg_match('/\bReply with \d+$/i', $message, $matches );

        //strip anything other than digits
        $recipient = preg_replace('/\D+/i', '', $recipient );

        if( $reply_phrase && isset($matches[0]) )
        {

            $parts = explode(" ", $matches[0] );
            if( isset( $parts[2] ) )
            {
                $reply_with = $parts[2];
            }
        }

        $paramCheck = $this->checkParams([
            'recipient' => $recipient,
            'message' => $message,
            'senderID' => $senderID,
            'securityCode' => $securityCode,
            'messageID' => $messageID,
            'reply_with' => $reply_with,
        ]);

        if( ! $paramCheck['success'] )
        {
            return $this->showError( $paramCheck['errorCode'], $paramCheck['errorText'],
                $paramCheck['errorDesc']);
        }

        //we're assuming sender ids are unique here
        $host = EnterpriseHost::where('senderID', $senderID )->where('enabled', 1)->first();

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
            SendTwilioSMS::dispatch( $host, $carrier, $recipient, $message, $messageID, $reply_with );

        }
        elseif( $carrier->api == 'thinq' )
        {
            SendThinqSMS::dispatch( $host, $carrier, $recipient, $message, $messageID, $reply_with );
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
            'message' => 'required|string|max:910', //thinq is 910, twilio is 1600
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

        $validator = Validator::make([
            'messageID' => $data['messageID'],
        ],[
            'messageID' => 'required|string|max:32',
        ]);

        if( $validator->fails() )
        {
            return [
                'success' => false,
                'errorCode' => 400,
                'errorText' => 'Function not supported',
                'errorDesc' => 'The messageID is invalid',
            ];
        }

        $validator = Validator::make([
            'reply_with' => $data['reply_with'],
        ],[
            'reply_with' => 'nullable|numeric',
        ]);

        if( $validator->fails() )
        {
            return [
                'success' => false,
                'errorCode' => 403,
                'errorText' => 'Invalid reply number from Amtelco',
                'errorDesc' => 'The reply number was included but is not an integer',
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
