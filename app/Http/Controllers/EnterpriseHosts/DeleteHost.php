<?php

namespace App\Http\Controllers\EnterpriseHosts;

use Exception;
use App\Jobs\LogEvent;
use App\EnterpriseHost;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteHost extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( EnterpriseHost $host )
    {
        LogEvent::dispatch(
            "{$host->senderID} deleted",
            get_class( $this ), 'info', json_encode($host->toArray()), Auth::user()->id ?? null
        );

        try{ $host->delete(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to delete host')]); }

        $statusHtml = "Enterprise host deleted!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
