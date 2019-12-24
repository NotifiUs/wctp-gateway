<?php

namespace App\Http\Controllers\EnterpriseHosts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowHosts extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('enterprise_hosts.show');
    }
}
