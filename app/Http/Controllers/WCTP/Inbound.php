<?php

namespace App\Http\Controllers\WCTP;

use Exception;
use App\Carrier;
use SimpleXMLElement;
use App\EnterpriseHost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client as TwilioClient;

class Inbound extends Controller
{

    public function __invoke(Request $request)
    {
        $wctp = new SimpleXMLElement( $request->getContent() );

        $recipient = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Recipient/@recipientID')[0];
        $message = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-Payload/wctp-Alphanumeric')[0];

        $senderID = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Originator/@senderID')[0];
        $securityCode = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Originator/@securityCode')[0];

        $host = EnterpriseHost::where('senderID', $senderID )->first();
        if( is_null( $host ) )
        {
            return view('WCTP.wctp-Failure')
                ->with('errorCode', '401' )
                ->with('errorText', 'Invalid senderID' )
                ->with('errorDesc', 'senderID does not live on this system');
        }

        try{
            if( $securityCode != decrypt( $host->securityCode ) )
            {
                return view('WCTP.wctp-Failure')
                    ->with('errorCode', '402' )
                    ->with('errorText', 'Invalid securityCode' )
                    ->with('errorDesc', 'securityCodes does not match');
            }
        }
        catch( Exception $e ){
            return view('WCTP.wctp-Failure')
                ->with('errorCode', '402' )
                ->with('errorText', 'Invalid securityCode' )
                ->with('errorDesc', 'Unable to decrypt securityCode');
        }

        $carrier = Carrier::where('enabled', 1)->orderBy('priority')->first();
        if( is_null($carrier)){
            return view('WCTP.wctp-Failure')
                ->with('errorCode', '606' )
                ->with('errorText', 'Service Unavailable' )
                ->with('errorDesc', 'No upstream carriers are enabled');
        }

        if( $carrier->api == 'twilio' )
        {
            try{
                $client = new TwilioClient( $host->twilio_account_sid, $host->twilio_auth_token );
            }
            catch( Exception $e ){
                return view('WCTP.wctp-Failure')
                    ->with('errorCode', '604' )
                    ->with('errorText', 'Internal Server Error' )
                    ->with('errorDesc', 'Unable to connect to upstream carrier');
            }

            try{
                $client->messages->create(
                    "+1{$recipient}",
                    array(
                        'from' => $carrier->numbers()->first()->e164,
                        'body' => $message
                    )
                );
            }
            catch( Exception $e ){
                return view('WCTP.wctp-Failure')
                    ->with('errorCode', '604' )
                    ->with('errorText', 'Internal Server Error' )
                    ->with('errorDesc', 'Unable to connect to upstream carrier');
            }

        }
        else
        {
            return view('WCTP.wctp-Failure')
                ->with('errorCode', '604' )
                ->with('errorText', 'Internal Server Error' )
                ->with('errorDesc', 'This carrier API is not yet implemented');
        }


        return view('WCTP.wctp-Confirmation')
            ->with('successCode', '200' )
            ->with( 'successText', 'Message queued for delivery' );

    }
}
