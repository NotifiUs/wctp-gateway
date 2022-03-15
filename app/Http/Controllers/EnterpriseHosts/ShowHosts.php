<?php

namespace App\Http\Controllers\EnterpriseHosts;

use App\Http\Controllers\Controller;
use App\Models\EnterpriseHost;

class ShowHosts extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $hosts = EnterpriseHost::all()->sortBy('enabled', null, true);
        return view('enterprise_hosts.show')->with('enterpriseHosts', $hosts );
    }
}
