<?php

namespace App\Http\Controllers\EnterpriseHosts;

use Exception;
use App\Jobs\LogEvent;
use App\Models\EnterpriseHost;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DisableHost extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( EnterpriseHost $host )
    {

        $host->enabled = 0;

        try{ $host->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to disable host')]); }

        LogEvent::dispatch(
            "{$host->senderID} disabled",
            get_class( $this ), 'info', json_encode($host->toArray()), Auth::user()->id ?? null
        );
        $statusHtml = "Enterprise host disabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
