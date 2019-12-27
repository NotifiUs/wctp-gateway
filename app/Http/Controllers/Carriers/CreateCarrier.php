<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreateCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request )
    {
        $this->validate( $request, [
            'name' => 'required|string|min:2|max:255|unique:carriers,name',
            'api' => 'required|in:twilio,thinq',
            'priority' => 'required|integer',
            'twilio_account_sid' => 'required_without:thinq_account_id',
            'twilio_auth_token' => 'required_with:twilio_account_sid',
            'thinq_account_id' => 'required_without:twilio_account_sid',
            'thinq_api_username' => 'required_with:thinq_api_token,thinq_account_id',
            'thinq_api_token' => 'required_with:thinq_api_username,thinq_account_id',
        ]);

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

            try{ $carrier->save(); }catch( Exception $e ){ return redirect()->back()->withInput()->withErrors([__('Unable to save carrier')]); }

            $statusHtml = "Carrier successfully created!";
            return redirect()->to('/carriers')
                ->with('status', $statusHtml);

        }

       return redirect()->to('/carriers')->withErrors(['Unable to create carrier']);


    }
}
