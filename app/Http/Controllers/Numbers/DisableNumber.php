<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Number;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DisableNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, \App\Number $number )
    {

        $number->enabled = 0;

        try{ $number->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to disable Phone Number')]); }

        $statusHtml = "Phone Number disabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
