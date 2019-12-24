<?php

namespace App\Http\Controllers\Carriers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowCarriers extends Controller
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
        return view('carriers.show');
    }
}
