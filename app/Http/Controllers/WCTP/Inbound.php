<?php

namespace App\Http\Controllers\WCTP;

use SimpleXMLElement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client as TwilioClient;

class Inbound extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $wctp = new SimpleXMLElement( $request->getContent() );

        $recipient = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-SubmitHeader/wctp-Recipient/@recipientID')[0];
        $message = (string)$wctp->xpath('/wctp-Operation/wctp-SubmitRequest/wctp-Payload/wctp-Alphanumeric')[0];

        $client = new TwilioClient( config('services.twilio.account'), config('services.twilio.token') );

        $client->messages->create(
            "+1{$recipient}",
            array(
                'from' => config('services.twilio.number'),
                'body' => $message
            )
        );

        return view('WCTP.wctp-Confirmation')
            ->with('successCode', '200' )
            ->with( 'successText', 'Message queued for delivery' );

    }
}
