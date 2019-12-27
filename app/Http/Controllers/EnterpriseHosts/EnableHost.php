<?php

namespace App\Http\Controllers\EnterpriseHosts;

use Exception;
use App\EnterpriseHost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EnableHost extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, EnterpriseHost $host )
    {

        $host->enabled = 1;

        try{ $host->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to enable host')]); }

        $statusHtml = "Enterprise host enabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
