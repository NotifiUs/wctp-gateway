<?php

namespace App\Http\Controllers\Carriers;

use App\Jobs\LogEvent;
use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255|unique:carriers,name',
            'api' => 'required|in:twilio,thinq',
            'priority' => 'required|integer|unique:carriers,priority',
            'twilio_account_sid' => 'required_without:thinq_account_id',
            'twilio_auth_token' => 'required_with:twilio_account_sid',
            'thinq_account_id' => 'required_without:twilio_account_sid',
            'thinq_api_username' => 'required_with:thinq_api_token,thinq_account_id',
            'thinq_api_token' => 'required_with:thinq_api_username,thinq_account_id',
        ]);

        if ( $validator->fails() ) { return redirect('/carriers' )->withErrors( $validator ); }

        if( $request->input('api') == 'twilio')
        {
            $carrier = new Carrier;
            $carrier->name = $request->input('name');
            $carrier->enabled = 0;
            $carrier->beta = 0;
            $carrier->priority = $request->input('priority');
            $carrier->twilio_account_sid = $request->input('twilio_account_sid');
            $carrier->twilio_auth_token = encrypt( $request->input('twilio_auth_token') );
            $carrier->api = $request->input('api');

            try{ $carrier->save(); }catch( Exception $e ){ return redirect()->to('/carriers')->withErrors([__('Unable to save carrier')]); }

            $statusHtml = "Carrier successfully created!";
            return redirect()->to('/carriers')
                ->with('status', $statusHtml);

        }
        elseif( $request->input('api') == 'thinq')
        {
            $carrier = new Carrier;
            $carrier->name = $request->input('name');
            $carrier->enabled = 0;
            $carrier->beta = 1;
            $carrier->priority = $request->input('priority');
            $carrier->thinq_account_id = $request->input('thinq_account_id');
            $carrier->thinq_api_username = $request->input('thinq_api_username');
            $carrier->thinq_api_token = encrypt( $request->input('thinq_api_token') );
            $carrier->api = $request->input('api');

            try{ $carrier->save(); }catch( Exception $e ){ return redirect()->to('/carriers')->withErrors([__('Unable to save carrier'), $e->getMessage()]); }

            LogEvent::dispatch(
                "{$carrier->name} ({$carrier->api}) created",
                get_class( $this ), 'info', json_encode($carrier->toArray()), Auth::user()->id ?? null
            );

            $statusHtml = "Carrier successfully created!";
            return redirect()->to('/carriers')
                ->with('status', $statusHtml);

        }

       return redirect()->to('/carriers')->withErrors(['Unable to create carrier']);


    }
}
