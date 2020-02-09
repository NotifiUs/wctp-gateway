<?php

namespace App\Http\Controllers\Account;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChangePassword extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);
        if( $validator->fails())
        {
            return redirect()->back()->withErrors( $validator->errors() );
        }

        if (! Hash::check($request->input('current_password'), Auth::user()->password )) {
            return redirect()->back()->withErrors(['Your current password was not correct.']);
        }
        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        try{
            $user->save();
        }
        catch( Exception $e ){ return redirect()->back()->withErrors( [$e->getMessage()]); }
        return redirect()->back()->withStatus('Your password has been updated!');
    }
}
