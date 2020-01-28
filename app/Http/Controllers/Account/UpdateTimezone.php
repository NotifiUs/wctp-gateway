<?php

namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateTimezone extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'timezone' => 'required|timezone',
        ]);
        if( $validator->fails())
        {
            return redirect()->back()->withErrors( $validator->errors() );
        }

        $user = Auth::user();
        $user->timezone = $request->input('timezone');
        try{
            $user->save();
        }
        catch( Exception $e ){ return redirect()->back()->withErrors( [$e->getMessage()]); }
        return redirect()->back()->withStatus('Your timezone has been updated!');
    }
}
