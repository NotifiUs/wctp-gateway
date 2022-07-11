<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Jobs\LogEvent;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class DisableMaintenanceMode extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        try {
            Artisan::call('up');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Unable to disable Maintenance Mode']);
        }

        LogEvent::dispatch(
            'Maintenance Mode disabled',
            get_class($this), 'notice', json_encode([]), Auth::user()->id ?? null
        );

        return redirect()->back()->with('status', 'Maintenance Mode has been disabled');
    }
}
