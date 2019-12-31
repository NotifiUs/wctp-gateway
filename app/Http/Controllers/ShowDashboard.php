<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\ServerStats;
use App\QueueStatus;
use Illuminate\Http\Request;

class ShowDashboard extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $queue = QueueStatus::get();
        $server = ServerStats::get();
        $checklist = Checklist::get();

        $inboundCount = number_format( mt_rand(0, 1000) );
        $outboundCount = number_format( mt_rand( 0, 1000) );

        return view('home')
            ->with('server', $server )
            ->with('queue', $queue )
            ->with('checklist', $checklist )
            ->with('inboundCount', $inboundCount )
            ->with('outboundCount', $outboundCount );
    }
}
