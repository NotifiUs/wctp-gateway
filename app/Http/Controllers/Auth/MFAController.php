<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use RobThree\Auth\TwoFactorAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MFAController extends Controller
{
    protected $tfa;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        try{
            $this->tfa = new TwoFactorAuth( config('app.name') );
        }
        catch( Exception $e ){ abort(500); }

    }

    public function show( Request $request )
    {
        $user = Auth::user();
        Session::forget('mfa_valid');

        return view('auth.mfa')->with( 'user', $user );
    }

    public function checkCode( Request $request )
    {
        $this->validate( $request, [
            'mfa_code' => 'required'
        ]);

        try {
            $valid = $this->tfa->verifyCode( decrypt( Auth::user()->mfa_secret), $request->input('mfa_code'));
        }
        catch( Exception $e )
        {
            return redirect()->back()->withErrors(['Unable to verify MFA code']);
        }

        if( ! $valid )
        {
            return redirect()->back()->withErrors(['Invalid MFA code provided']);
        }

        Session::put('mfa_valid', true );

        return redirect()->to('/home');
    }

}
