<?php

namespace App\Http\Controllers\System;

use App\Checklist;
use App\ServerStats;
use App\QueueStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowSysInfo extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function __invoke(Request $request)
    {
        $server = ServerStats::get();
        $queue = QueueStatus::get();
        $advanced = ServerStats::advanced();
        $checklist = Checklist::get();

        return view('system.information' )
                ->with('server', $server )
                ->with('queue', $queue )
                ->with('advanced', $advanced )
                ->with('checklist', $checklist );
    }
}
