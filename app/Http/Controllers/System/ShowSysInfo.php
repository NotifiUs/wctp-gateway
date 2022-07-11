<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\QueueStatus;
use App\Models\ServerStats;

class ShowSysInfo extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $server = ServerStats::get();
        $queue = QueueStatus::get();
        $advanced = ServerStats::advanced();
        $checklist = Checklist::get();

        return view('system.information')
                ->with('server', $server)
                ->with('queue', $queue)
                ->with('advanced', $advanced)
                ->with('checklist', $checklist);
    }
}
