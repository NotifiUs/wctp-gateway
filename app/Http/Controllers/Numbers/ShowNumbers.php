<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Number;
use App\Carrier;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\RequestException;

class ShowNumbers extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $carriers = Carrier::all();
        $available = [];
        $active = Number::all()->toArray();

        foreach( $carriers as $carrier )
        {
            if( $carrier->api == 'twilio')
            {
                try {
                    $twilio = new Client( $carrier->twilio_account_sid, decrypt( $carrier->twilio_auth_token ) );
                    $incomingPhoneNumbers = $twilio->incomingPhoneNumbers->read(array(
                        ['capabilities' => [
                            'sms' => 1
                        ]]
                    ), 100);

                    foreach ( $incomingPhoneNumbers as $record ) {

                        $available[] = [
                            'id' => $record->sid,
                            'api' => $carrier->api,
                            'number' => $record->phoneNumber,
                            'carrier' => $carrier,
                            'details' => $record->toArray(),
                            'sms_enabled' => $record->capabilities['sms'],
                        ];
                    }
                }
                catch( Exception $e )
                {
                    //return redirect()->to('/home')->withErrors(['Unable to view numbers.']);
                }

            }
            elseif( $carrier->api == 'thinq')
            {
                $url = "/origination/did/search2/did/{$carrier->thinq_account_id}";
                $guzzle = new Guzzle(
                    ['base_uri' => 'https://api.thinq.com',]
                );
                try{
                    $res = $guzzle->get( $url, ['auth' => [ $carrier->thinq_api_username, decrypt($carrier->thinq_api_token)]]);
                }
                catch( RequestException $e ) {
                    dd( $e );
                }
                catch( Exception $e ){
                    dd( $e );
                }

                $thinq_numbers = json_decode( (string)$res->getBody(), true );

                if( $thinq_numbers['total_rows'] > 0 )
                {
                    foreach( $thinq_numbers['rows'] as $thinq_number )
                    {
                        $available[] = [
                            'id' => $thinq_number['id'],
                            'api' => $carrier->api,
                            'number' => "+{$thinq_number['id']}",
                            'carrier' => $carrier,
                            'details' => $thinq_number,
                            'sms_enabled' => $thinq_number['provisioned']
                        ];
                    }
                }
            }
        }

        foreach( $available as $key => $avail )
        {
            foreach( $active as $inuse )
            {
                if( $avail['id'] == $inuse['identifier'] )
                {
                    unset($available[$key]);
                }
            }
        }

        return view('numbers.show')->with('available', $available )->with('active', $active );
    }
}
