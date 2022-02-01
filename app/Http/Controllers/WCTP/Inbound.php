<?php /** @noinspection PhpComposerExtensionStubsInspection */

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
    public $wctpMethodType = 'SubmitRequest';

    public function __invoke(Request $request)
    {
        $xmlCheck = $request->getContent() ?? '';

        libxml_use_internal_errors(true);

        if($xmlCheck === '' )
        {
            return $this->showError(301, 'Cannot Parse Input',
                'Message body is empty');
        }

        $wctp = simplexml_load_string( $xmlCheck );
        $xmlError = libxml_get_last_error();
        libxml_clear_errors();

        if( $wctp === false || $xmlError !== false ) {

            return $this->showError(302, 'XML Validation Error',
                'Unable to parse malformed or invalid XML.');
        }

        $recipientXPath = '/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Recipient/@recipientID';
        $messageXPath = '/wctp-Operation/wctp-SubmitRequest/wctp-Payload/wctp-Alphanumeric';
        $messageIDXPath = '/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-MessageControl/@messageID';
        $senderIDXPath = '/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Originator/@senderID';
        $securityCodeXPath = '/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Originator/@securityCode';

        $wctpTransientMethodOperation = $wctp->xpath('/wctp-Operation/wctp-SubmitClientMessage')[0] ?? null;

        if($wctpTransientMethodOperation !== null && $wctpTransientMethodOperation->count() > 0)
        {
            $this->wctpMethodType = 'SubmitClientMessage';

            $recipientXPath = '/wctp-Operation/wctp-SubmitClientMessage/wctp-SubmitClientHeader/wctp-Recipient/@recipientID';
            $messageXPath = '/wctp-Operation/wctp-SubmitClientMessage/wctp-Payload/wctp-Alphanumeric';
            $senderIDXPath = '/wctp-Operation/wctp-SubmitClientMessage/wctp-SubmitClientHeader/wctp-ClientOriginator/@senderID';
            $securityCodeXPath = '/wctp-Operation/wctp-SubmitClientMessage/wctp-SubmitClientHeader/wctp-ClientOriginator/@miscInfo';
            $messageIDXPath = '/wctp-Operation/wctp-SubmitClientMessage/wctp-SubmitClientHeader/wctp-ClientMessageControl/@messageID';
        }

        try{
            $recipient = (string)$wctp->xpath($recipientXPath)[0] ?? null;
            $senderID = (string)$wctp->xpath($senderIDXPath)[0] ?? null;
            $securityCode = (string)$wctp->xpath($securityCodeXPath)[0] ?? null;
            if($this->wctpMethodType === 'SubmitMessage' )
            {
                $messageID = (string)$wctp->xpath($messageIDXPath)[0] ?? null;
            }
            else
            {
                $messageID = null;
            }
        }
        catch(Exception $e )
        {
            return $this->showError(302, 'XML Validation Error',
                'recipientID, wctp-Alphanumeric, senderID, and securityCode (or miscInfo) are required.');
        }

        try{
            $message = (string)$wctp->xpath($messageXPath)[0] ?? null;
        }
        catch(Exception $e)
        {
            $message = null;
        }

        if($message === null)
        {
            try{
                $message = (string)$wctp->xpath("/wctp-Operation/wctp-{$this->wctpMethodType}/wctp-Payload/wctp-TransparentData")[0] ?? null;
            }
            catch(Exception $e)
            {
                $message = null;
            }

            if($message !== null )
            {
                $message = base64_decode($message);
            }
        }

        if($this->wctpMethodType === 'SubmitMessage' )
        {
            if($messageID === null )
            {
                return $this->showError(302, 'XML Validation Error',
                    'mnessageID is required.');
            }
        }

        if( $recipient === null || $message === null || $senderID === null || $securityCode === null )
        {
            return $this->showError(302, 'XML Validation Error',
                'recipientID, wctp-Alphanumeric, senderID, and securityCode (or miscInfo) are required.');
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

        if( $host === null )
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

        if( $carrier === null ){
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

        if( $this->wctpMethodType === 'SubmitClientMessage')
        {
            return view('WCTP.wctp-SubmitClientResponseFailure')
                ->with('errorCode', $code )
                ->with('errorText', $text )
                ->with('errorDesc', $desc );
        }
        else
        {
            return view('WCTP.wctp-Failure')
                ->with('errorCode', $code )
                ->with('errorText', $text )
                ->with('errorDesc', $desc );
        }
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
            'message' => 'required|string|max:1600', //thinq is 910, twilio is 1600
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

        if($this->wctpMethodType === 'SubmitMessage' )
        {
            $validator = Validator::make([
                'messageID' => $data['messageID'],
            ], [
                'messageID' => 'required|string|max:32',
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'errorCode' => 400,
                    'errorText' => 'Function not supported',
                    'errorDesc' => 'The messageID is invalid',
                ];
            }
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
                'errorText' => 'Invalid reply number included',
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
