<?php

namespace App\Http\Controllers\Numbers;

use App\Http\Controllers\Controller;
use App\Models\Number;

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
