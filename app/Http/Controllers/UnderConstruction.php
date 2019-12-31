<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnderConstruction extends Controller
{
    public function __invoke(Request $request)
    {
        return view('under-construction');
    }
}
