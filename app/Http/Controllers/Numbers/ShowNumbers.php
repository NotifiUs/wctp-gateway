<?php

namespace App\Http\Controllers\Numbers;

use App\Number;
use App\Http\Controllers\Controller;

class ShowNumbers extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke($available = null )
    {
        $active = Number::all()->toArray();

        return view('numbers.show')->with('active', $active );
    }
}
