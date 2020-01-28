<?php

namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ChangeEmail extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => "required|email|unique:users,email," . Auth::user()->id,
        ]);
        if( $validator->fails())
        {
            return redirect()->back()->withErrors( $validator->errors() );
        }

        $user = Auth::user();

        if( $user->email != $request->input('email'))
        {
            $user->email_verified_at = null;
        }
        
        $user->email = $request->input('email');

        try{
            $user->save();
        }
        catch( Exception $e ){ return redirect()->back()->withErrors( [$e->getMessage()]); }
        return redirect()->back()->withStatus('Your email has been updated!');
    }
}
