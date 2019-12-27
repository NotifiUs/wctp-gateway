<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DisableCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, Carrier $carrier )
    {

        $carrier->enabled = 0;

        try{ $carrier->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to disable carrier')]); }

        $statusHtml = "Carrier disabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
