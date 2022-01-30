<?php

namespace App\Http\Controllers;

use App\Message;
use App\EventLog;
use App\Checklist;
use Carbon\Carbon;
use App\ServerStats;
use App\QueueStatus;

class ShowDashboard extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $queue = QueueStatus::get();
        $server = ServerStats::get();
        $checklist = Checklist::get();
        $events = EventLog::take(10)->orderBy('created_at', 'desc')->get();

        $activityPeriod = Carbon::now()->subHours(24);
        $inboundCount = Message::where('direction', 'inbound')->where('created_at', '>=', $activityPeriod )->count();
        $outboundCount = Message::where('direction', 'outbound')->where('created_at', '>=', $activityPeriod )->count();

        return view('home')
            ->with('server', $server )
            ->with('queue', $queue )
            ->with('checklist', $checklist )
            ->with('inboundCount', $inboundCount )
            ->with('outboundCount', $outboundCount )
            ->with('activityPeriod', $activityPeriod )
            ->with('events', $events );
    }
}
