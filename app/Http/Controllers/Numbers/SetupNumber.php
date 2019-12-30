<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Carrier;
use App\Number;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;

class SetupNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, \App\Number $number )
    {

        if( $number->provision() !== true )
        {
            return redirect()->back()->withErrors(['Unable to provision number with carrier.'] );
        }

        $statusHtml = "Number successfully setup!";
        return redirect()->to('/numbers')
            ->with('status', $statusHtml);
    }
}
