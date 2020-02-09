<?php

namespace App\Http\Controllers\Account;

use Exception;
use Illuminate\Http\Request;
use RobThree\Auth\TwoFactorAuth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ShowMFA extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $secret = null;
        $image = null;
        $user = Auth::user();

        if( ! $user->mfa_secret )
        {
            try{
                $tfa = new TwoFactorAuth( config('app.name') );
                $secret = $tfa->createSecret();
                $image = $tfa->getQRCodeImageAsDataUri( $user->email , $secret);
            }
            catch( Exception $e )
            {
                abort(500);
            }
        }

        return view('account.mfa' )
            ->with('user', $user )
            ->with('mfa_shared', $secret )
            ->with('image', $image );
    }
}
