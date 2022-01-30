<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Number;
use App\Jobs\LogEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DisableNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Number $number )
    {


        $number->enabled = 0;

        try{ $number->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to disable Phone Number')]); }

        LogEvent::dispatch(
            "{$number->e164} disabled",
            get_class( $this ), 'info', json_encode($number->toArray()), Auth::user()->id ?? null
        );
        $statusHtml = "Phone Number disabled!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
