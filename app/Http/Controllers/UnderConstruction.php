<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UnderConstruction extends Controller
{
    public function __invoke(): View
    {
        return view('under-construction');
    }
}
