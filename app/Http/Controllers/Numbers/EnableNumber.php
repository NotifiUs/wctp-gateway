<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Number;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EnableNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, \App\Number $number  )
    {

        $number->enabled = 1;

        try{ $number->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to enable Phone Number')]); }

        $statusHtml = "Phone Number enabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
