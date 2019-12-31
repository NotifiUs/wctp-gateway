<?php

namespace App\Http\Controllers\Carriers;

use App\Jobs\LogEvent;
use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, Carrier $carrier )
    {
        LogEvent::dispatch(
            "{$carrier->name} ({$carrier->api}) deleted",
            get_class( $this ), 'info', json_encode($carrier->toArray()), Auth::user()->id ?? null
        );

        try{ $carrier->delete(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to delete carrier')]); }

        $statusHtml = "Carrier successfully deleted!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
