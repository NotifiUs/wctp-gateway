<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\EventLog;
use App\Models\Checklist;
use Carbon\Carbon;
use App\Models\ServerStats;
use App\Models\QueueStatus;
use Illuminate\View\View;

class ShowDashboard extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(): View
    {
        $queue = QueueStatus::get();
        $server = ServerStats::get();
        $checklist = Checklist::get();
        $events = EventLog::take(10)->orderBy('created_at', 'desc')->get();

        $activityPeriod = Carbon::now()->subHours(24);
        $inboundCount = Message::where('direction', 'inbound')->where('created_at', '>=', $activityPeriod )->count();
        $outboundCount = Message::where('direction', 'outbound')->where('created_at', '>=', $activityPeriod )->count();

        return view('home')
            ->with('queue', $queue )
            ->with('events', $events )
            ->with('server', $server )
            ->with('checklist', $checklist )
            ->with('inboundCount', $inboundCount )
            ->with('outboundCount', $outboundCount )
            ->with('activityPeriod', $activityPeriod );
    }
}
