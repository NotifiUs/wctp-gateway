<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Models\Number;
use App\Jobs\LogEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AssignHost extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, Number $number )
    {
        $this->validate( $request, [
            'enterprise_host_id' => 'required|exists:enterprise_hosts,id'
        ]);

        $number->enterprise_host_id = $request->input('enterprise_host_id' );

        try{ $number->save(); }catch( Exception $e ){ return redirect()->to('/numbers')->withErrors([__('Unable to assign Enterprise Host')]); }


        LogEvent::dispatch(
            "{$number->e164} host assigned",
            get_class( $this ), 'info', json_encode($number->toArray()), Auth::user()->id ?? null
        );

       $statusHtml = "Number successfully assigned to host!";
        return redirect()->to('/numbers')
            ->with('status', $statusHtml);
    }
}
