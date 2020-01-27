<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ShowSystem extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $maintenanceMode = [];
        if( App::isDownForMaintenance() )
        {
            $maintenanceMode = json_decode( File::get( storage_path('framework/down')), true );
        }

        return view('system.show' )->with('clientIp', $request->getClientIp() )
            ->with('maintenanceMode', $maintenanceMode );
    }
}