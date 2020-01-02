<?php

namespace App\Http\Controllers\System;

use Exception;
use App\Jobs\LogEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Artisan;

class EnableMaintenanceMode extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function __invoke(Request $request)
    {
        $this->validate( $request, [
            'message' => 'nullable|string',
            'retry' => 'nullable|integer|min:1',
        ]);

        $message = $request->input('message') ?? 'General Maintenance';
        $retry = $request->input('retry') ?? 15;

        $params = [
            "--message" => $message,
            "--retry" => $retry,
            "--allow" => $request->getClientIp(),
        ];

        try{
            Artisan::call('down', $params );
        }
        catch( Exception $e )
        {
            return redirect()->back()->withErrors([
                'Unable to enable Maintenance Mode',
                $e->getMessage()
            ]);
        }

        LogEvent::dispatch(
            "Maintenance Mode enabled",
            get_class( $this ), 'alert', json_encode($params), Auth::user()->id ?? null
        );

        return redirect()->back()->with('status', 'Maintenance Mode has been enabled');
    }
}
