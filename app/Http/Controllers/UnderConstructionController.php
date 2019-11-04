<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnderConstructionController extends Controller
{
    public function index( Request $request )
    {
        return view('under-construction');
    }
}
