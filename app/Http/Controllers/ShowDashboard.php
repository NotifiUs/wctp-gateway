<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\ServerStats;
use App\QueueStatus;
use App\EnterpriseHost;
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

        $checklist = [];

        if( EnterpriseHost::where('enabled', 1)->count()  === 0 )
        {
            $checklist[] = [
                'description' => 'No enabled Enterprise Hosts',
                'link' => '/hosts'
            ];
        }
        if( Carrier::where('enabled', 1)->count() === 0 )
        {
            $checklist[] = [
                'description' => 'No enabled Carrier API providers',
                'link' => '/carriers'
            ];
        }

        $enabledNumbers = false;

        foreach( Carrier::where('enabled', 1)->get() as $carrier )
        {
            foreach( $carrier->numbers as $n )
            {
                if( $n->enabled )
                {
                    $enabledNumbers = true;
                }
            }
        }

        if( $enabledNumbers !== true )
        {
            $checklist[] = [
                'description' => 'No enabled Phone Numbers',
                'link' => '/numbers',
            ];
        }

        $messageCount = number_format( mt_rand(0, 25000) );
        $errorCount = number_format( mt_rand( 0, 1000) );

        return view('home')
            ->with('server', $server )
            ->with('queue', $queue )
            ->with('checklist', $checklist )
            ->with('messageCount', $messageCount )
            ->with('errorCount', $errorCount );
    }
}
