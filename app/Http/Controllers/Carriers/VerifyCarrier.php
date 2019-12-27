<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        if ($request->input('twilio_account_sid') && !$request->input('thinq_account_id'))
        {
            //twilio account
            dd( 'twilio!');
        }
        elseif ($request->input('thinq_account_id') && !$request->input('twilio_account_sid'))
        {
            //thinq account
            dd( 'thinq!');
        }
        else
        {
            return redirect()->back()->withErrors(['Unable to validate which carrier you intended to use.']);
        }

    }
}
