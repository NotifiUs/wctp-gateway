<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Number;
use App\Carrier;
use Twilio\Rest\Client;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\RequestException;

class ShowAvailable extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request, $available = null )
    {
        $carriers = Carrier::all();
        $available = [];
        $active = Number::all()->toArray();
        $exclude = [];

        foreach( $carriers as $carrier )
        {
            if( $carrier->api == 'twilio')
            {
                //get list of numbers from messaging services
                try {
                    $twilio = new Client( $carrier->twilio_account_sid, decrypt( $carrier->twilio_auth_token ) );
                    $services = $twilio->messaging->v1->services->read(100, 100);


                    foreach ( $services as $record ) {
                        //check to see if it has phonenumbers or shortcodes
                        $hasNumbers = false;
                        $hasShortCodes = false;
                        $serviceAddons = [];
                        $service_name = $record->friendlyName;
                        foreach( $record->phoneNumbers->read(100, 100) as $num )
                        {
                            $hasNumbers = true;
                            $service_name = $num->phoneNumber;
                            $exclude[] = $num->sid;
                            $serviceAddons['numbers'][] = $num->toArray();
                        }

                        foreach( $record->shortCodes->read(100, 100) as $shortcode )
                        {
                            $hasShortCodes = true;
                            $service_name = $shortcode->shortCode;
                            $exclude[] = $shortcode->sid;
                            $serviceAddons['shortcodes'][] = $shortcode->toArray();
                        }

                        $available[] = [
                            'id' => $record->sid,
                            'api' => $carrier->api,
                            'type' => 'Messaging Service',
                            'number' => $service_name,
                            'carrier' => $carrier,
                            'details' => Arr::dot( array_merge($record->toArray(), $serviceAddons ) ),
                            'sms_enabled' => $hasNumbers || $hasShortCodes,
                        ];
                    }
                }
                catch( Exception $e ) {}


                //get list of numbers
                try {
                    $twilio = new Client( $carrier->twilio_account_sid, decrypt( $carrier->twilio_auth_token ) );
                    $incomingPhoneNumbers = $twilio->incomingPhoneNumbers->read(array(
                        ['capabilities' => [
                            'sms' => 1
                        ]]
                    ), 100);

                    foreach ( $incomingPhoneNumbers as $record ) {

                        if( in_array( $record->sid, $exclude ) )
                        {
                            $available[] = [
                                'id' => $record->sid,
                                'api' => $carrier->api,
                                'type' => 'Phone Number',
                                'number' => $record->phoneNumber,
                                'carrier' => $carrier,
                                'details' => Arr::dot($record->toArray()),
                                'sms_enabled' => 0,
                            ];
                        }
                        else
                        {
                            $available[] = [
                                'id' => $record->sid,
                                'api' => $carrier->api,
                                'type' => 'Phone Number',
                                'number' => $record->phoneNumber,
                                'carrier' => $carrier,
                                'details' => Arr::dot($record->toArray()),
                                'sms_enabled' => $record->capabilities['sms'],
                            ];
                        }

                    }
                }
                catch( Exception $e ){}
            }
            elseif( $carrier->api == 'thinq')
            {
                $url = "/origination/did/search2/did/{$carrier->thinq_account_id}?page=1&rows=1000";
                $guzzle = new Guzzle(
                    ['base_uri' => 'https://api.thinq.com',]
                );
                try{
                    $res = $guzzle->get( $url, ['auth' => [ $carrier->thinq_api_username, decrypt($carrier->thinq_api_token)]]);
                }
                catch( RequestException $e ) {}
                catch( Exception $e ){}

                $thinq_numbers = json_decode( (string)$res->getBody(), true );

                if( $thinq_numbers['total_rows'] > 0 )
                {
                    foreach( $thinq_numbers['rows'] as $thinq_number )
                    {
                        $available[] = [
                            'id' => $thinq_number['id'],
                            'api' => $carrier->api,
                            'type' => 'Phone Number',
                            'number' => "+{$thinq_number['id']}",
                            'carrier' => $carrier,
                            'details' => Arr::dot( $thinq_number ),
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

        return view('numbers.available')->with('available', $available );
    }
}
