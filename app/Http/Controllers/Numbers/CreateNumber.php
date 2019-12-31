<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Number;
use App\Jobs\LogEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CreateNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request )
    {
        $this->validate( $request, [
            'identifier' => 'required|string|unique:numbers,identifier',
            'e164' => 'required|unique:numbers,e164',
            'carrier_id' => 'required|exists:carriers,id'
        ]);

        $number = new Number;
        $number->identifier = $request->input('identifier');
        $number->e164 = $request->input('e164');
        $number->carrier_id = $request->input('carrier_id' );

        try{ $number->save(); }catch( Exception $e ){ return redirect()->to('/numbers')->withErrors([__('Unable to save number')]); }

       if( $number->provision() !== true )
       {
           return redirect()->to('/numbers')->withErrors(['Unable to provision details with carrier.']);
       }

        LogEvent::dispatch(
            "{$number->e164} provisioned",
            get_class( $this ), 'info', json_encode($number->toArray()), Auth::user()->id ?? null
        );

       $statusHtml = "Number successfully associated!";
        return redirect()->to('/numbers')
            ->with('status', $statusHtml);
    }
}
