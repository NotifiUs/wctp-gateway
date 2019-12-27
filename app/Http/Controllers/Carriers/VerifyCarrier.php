<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\RequestException;

class VerifyCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request )
    {
        $this->validate($request, [
            'twilio_account_sid' => 'required_without:thinq_account_id',
            'twilio_auth_token' => 'required_with:twilio_account_sid',
            'thinq_account_id' => 'required_without:twilio_account_sid',
            'thinq_api_username' => 'required_with:thinq_api_token,thinq_account_id',
            'thinq_api_token' => 'required_with:thinq_api_username,thinq_account_id',
        ]);

        if( strlen( $request->input('twilio_account_sid')))
        {
            try {
                $twilio = new Client( $request->input('twilio_account_sid'), $request->input('twilio_auth_token'));
                $account = $twilio->api->v2010->accounts($request->input('twilio_account_sid'))->fetch();
            }
            catch( Exception $e )
            {
                return redirect()->to('/carriers')->withErrors(['Unable to connect to Twilio account']);
            }

            return view('carriers.twilio-verify')->with('account', $account->toArray() );

        }
        else
        {
            $url = "/account/{$request->input('thinq_account_id')}/balance";
            $guzzle = new Guzzle(
                ['base_uri' => 'https://api.thinq.com',]
            );
            try{
                $res = $guzzle->request('GET', $url, ['auth' => [ $request->input('thinq_api_username'), $request->input('thinq_api_token')]]);
            }
            catch( RequestException $e ) {
                return redirect()->to('/carriers')->withErrors(["Unable to connect to ThinQ account: {$e->getResponse()->getReasonPhrase()}"]);
            }
            catch( Exception $e ){
                return redirect()->to('/carriers')->withErrors(['Unable to connect to ThinQ account']);
            }

            $balance = json_decode( (string)$res->getBody(), true );

            $account = [
                'balance' => $balance['balance'],
                'account_id' => $request->input('thinq_account_id'),
                'api_username' => $request->input('thinq_api_username'),
                'api_token' => $request->input('thinq_api_token'),
            ];
            return view('carriers.thinq-verify')->with('account', $account );
        }

        return redirect()->to('/carriers');

    }
}
