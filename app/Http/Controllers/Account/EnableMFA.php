<?php

namespace App\Http\Controllers\Account;

use Exception;
use Illuminate\Http\Request;
use RobThree\Auth\TwoFactorAuth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class EnableMFA extends Controller
{
    protected $tfa;

    public function __construct()
    {
        $this->middleware('auth');

        try{
            $parsed = parse_url( config('app.url'), PHP_URL_HOST );
            $this->tfa = new TwoFactorAuth( $parsed ?? config('app.name') );
        }
        catch( Exception $e ){ abort(500); }
    }

    public function __invoke(Request $request)
    {
        $this->validate( $request, [
            'mfa_code' => 'required',
            'mfa_shared' => 'required',
        ]);

        try {
            $valid = $this->tfa->verifyCode( $request->input('mfa_shared'), $request->input('mfa_code'));
        }
        catch( Exception $e )
        {
            return redirect()->back()->withErrors(['Unable to verify MFA code']);
        }

        if( ! $valid )
        {
            return redirect()->back()->withErrors(['Invalid MFA code provided']);
        }
        $user = Auth::user();
        $user->mfa_secret = encrypt( $request->input('mfa_shared') );
        try{
            $user->save();
        }
        catch( Exception $e ){ return redirect()->back()->withErrors([$e->getMessage() ] ); }

        Session::put('mfa_valid', true );

        return redirect()->back()->withStatus('Multi-factor authentication enabled');
    }
}
