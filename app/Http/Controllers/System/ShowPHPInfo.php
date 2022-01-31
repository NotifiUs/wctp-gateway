<?php

namespace App\Http\Controllers\System;

use App\Jobs\LogEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShowPHPInfo extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function __invoke()
    {
        LogEvent::dispatch(
            "Viewed phpinfo()",
            get_class( $this ), 'notice', json_encode([]), Auth::user()->id ?? null
        );

        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        return response( $phpinfo );
    }
}
