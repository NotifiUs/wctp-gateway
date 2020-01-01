<?php

namespace App\Http\Controllers\System;

use App\Jobs\LogEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShowPHPInfo extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function __invoke(Request $request)
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        LogEvent::dispatch(
            "Viewed phpinfo()",
            get_class( $this ), 'notice', json_encode([]), Auth::user()->id ?? null
        );

        return response( $phpinfo );
    }
}
