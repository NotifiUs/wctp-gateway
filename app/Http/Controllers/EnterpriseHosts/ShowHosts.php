<?php

namespace App\Http\Controllers\EnterpriseHosts;

use App\EnterpriseHost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowHosts extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $hosts = EnterpriseHost::all()->sortBy('enabled', null, true);
        return view('enterprise_hosts.show')->with('enterpriseHosts', $hosts );
    }
}
