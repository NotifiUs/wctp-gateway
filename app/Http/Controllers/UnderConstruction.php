<?php

namespace App\Http\Controllers;

class UnderConstruction extends Controller
{
    public function __invoke()
    {
        return view('under-construction');
    }
}
