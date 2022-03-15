<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\Carrier;

class ShowCarriers extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $carriers = Carrier::all()->sortBy('priority')->sortBy('enabled', null, true);

        return view('carriers.show')->with('carriers', $carriers );
    }
}
