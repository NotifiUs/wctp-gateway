<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use App\Models\Carrier;
use App\Jobs\LogEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EnableCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Carrier $carrier )
    {

        if( $carrier->numbers()->count() == 0 )
        {
            return redirect()->back()->withErrors(['Please enable at least one phone number']);
        }

        $carrier->enabled = 1;

        try{ $carrier->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to enable carrier')]); }

        LogEvent::dispatch(
            "{$carrier->name} ({$carrier->api}) enabled",
            get_class( $this ), 'info', json_encode($carrier->toArray()), Auth::user()->id ?? null
        );

        $statusHtml = "Carrier enabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
