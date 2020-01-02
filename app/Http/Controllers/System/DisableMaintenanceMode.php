<?php

namespace App\Http\Controllers\System;

use Exception;
use App\Jobs\LogEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Artisan;

class DisableMaintenanceMode extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function __invoke(Request $request)
    {
        try{
            Artisan::call('up');
        }
        catch( Exception $e )
        {
            return redirect()->back()->withErrors(['Unable to disable Maintenance Mode']);
        }

        LogEvent::dispatch(
            "Maintenance Mode disabled",
            get_class( $this ), 'notice', json_encode([]), Auth::user()->id ?? null
        );

        return redirect()->back()->with('status', 'Maintenance Mode has been disabled');
    }
}
