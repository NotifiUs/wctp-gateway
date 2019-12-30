<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EnableCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, Carrier $carrier )
    {

        if( $carrier->numbers()->count() == 0 )
        {
            return redirect()->back()->withErrors(['Please enable at least one phone number']);
        }

        $carrier->enabled = 1;

        try{ $carrier->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to enable carrier')]); }

        $statusHtml = "Carrier enabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
