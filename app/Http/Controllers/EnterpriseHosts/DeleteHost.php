<?php

namespace App\Http\Controllers\EnterpriseHosts;

use Exception;
use App\EnterpriseHost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteHost extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, EnterpriseHost $host )
    {
        try{ $host->delete(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to delete host')]); }

        $statusHtml = "Enterprise host deleted!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
