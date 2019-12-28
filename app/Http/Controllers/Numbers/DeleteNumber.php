<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Number;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, \App\Number $number )
    {
        try{ $number->delete(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to delete Phone Number')]); }

        $statusHtml = "Phone Number released!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
