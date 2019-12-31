<?php

namespace App\Http\Controllers\Numbers;

use App\Jobs\LogEvent;
use Exception;
use App\Number;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

        LogEvent::dispatch(
            "{$number->e164} enabled",
            get_class( $this ), 'info', json_encode($number->toArray()), Auth::user()->id ?? null
        );
        $statusHtml = "Phone Number enabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
