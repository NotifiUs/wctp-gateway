<?php

namespace App\Http\Controllers\Account;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class DisableMFA extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        Session::forget('mfa_valid');
        $user = Auth::user();
        $user->mfa_secret = null;
        try{
            $user->save();
        }
        catch( Exception $e ){ return redirect()->back()->withErrors([$e->getMessage()]); }

        return redirect()->back()->withStatus('Multi-factor authentication removed from your account.');
    }
}
