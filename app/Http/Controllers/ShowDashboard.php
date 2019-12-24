<?php

namespace App\Http\Controllers;

use App\ServerStats;
use App\QueueStatus;
use Illuminate\Http\Request;

class ShowDashboard extends Controller
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
        $queue = QueueStatus::get();
        $server = ServerStats::get();

        return view('home')
            ->with('server', $server )
            ->with('queue', $queue );
    }
}
