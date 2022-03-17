<?php

namespace App\Http\Controllers\Numbers;

use App\Models\Number;
use App\Http\Controllers\Controller;

class ShowNumbers extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $active = Number::all()->toArray();

        return view('numbers.show')->with('active', $active);
    }
}
