<?php

namespace App\Http\Controllers\Numbers;

use App\Models\Number;
use App\Jobs\LogEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SetupNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Number $number )
    {
        if( $number->provision() !== true )
        {
            return redirect()->back()->withErrors(['Unable to provision number with carrier.'] );
        }

        LogEvent::dispatch(
            "{$number->e164} setup",
            get_class( $this ), 'info', json_encode($number->toArray()), Auth::user()->id ?? null
        );
        $statusHtml = "Number successfully setup!";
        return redirect()->to('/numbers')
            ->with('status', $statusHtml);
    }
}
