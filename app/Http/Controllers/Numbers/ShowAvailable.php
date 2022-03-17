<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Models\Number;
use App\Models\Carrier;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Drivers\DriverFactory;
use App\Http\Controllers\Controller;

class ShowAvailable extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request ): View
    {
        $pages = null;
        $available = [];
        $carriers = Carrier::all();
        $active = Number::all()->toArray();

        foreach( $carriers as $carrier )
        {
            try{
                $driverFactory = new DriverFactory( $carrier->api );
                $driver = $driverFactory->loadDriver();
            }
            catch( Exception $e ){ continue; }

            $results = $driver->getAvailableNumbers($request, $carrier);

            $available = array_merge( $available, $results['available']);

            if($results['pages'] !== null )
            {
                $pages = $results['pages'];
            }
        }

        foreach( $available as $key => $avail )
        {
            foreach( $active as $inuse )
            {
                if( $avail['id'] == $inuse['identifier'] )
                {
                    unset($available[$key]);
                }
            }
        }

        return view('numbers.available')->with('available', $available )->with('pages', $pages ?? 0);
    }
}
