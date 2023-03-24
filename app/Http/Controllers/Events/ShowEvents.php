<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\EventLog;
use Illuminate\Http\Request;

class ShowEvents extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $sourceList = collect(EventLog::distinct('source')->get('source')->toArray() ?? [])->flatten();
        $sourceFilter = $request->get('source') ?? null;

        if($sourceFilter === null){
            $events = EventLog::orderBy('created_at', 'desc')->paginate(25);
        }
        else{
            $events = EventLog::where('source', $sourceFilter)->orderBy('created_at', 'desc')->paginate(25);
        }

        return view('events.show')
            ->with('events', $events)
            ->with('sourceList', $sourceList)
            ->with('sourceFilter', $sourceFilter);
    }
}
