<?php

namespace App\Http\Controllers\Numbers;

use App\Jobs\LogEvent;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, \App\Number $number )
    {
        LogEvent::dispatch(
            "{$number->e164} released",
            get_class( $this ), 'info', json_encode($number->toArray()), Auth::user()->id ?? null
        );

        try{ $number->delete(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to delete Phone Number')]); }


        $statusHtml = "Phone Number released!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
