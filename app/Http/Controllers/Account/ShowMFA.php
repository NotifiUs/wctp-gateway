<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use RobThree\Auth\TwoFactorAuth;

class ShowMFA extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $secret = null;
        $image = null;
        $user = Auth::user();

        if (! $user->mfa_secret) {
            try {
                $parsed = parse_url(config('app.url'), PHP_URL_HOST);
                $tfa = new TwoFactorAuth($parsed ?? config('app.name'));
                $secret = $tfa->createSecret();
                $image = $tfa->getQRCodeImageAsDataUri($user->email, $secret);
            } catch (Exception $e) {
                abort(500);
            }
        }

        return view('account.mfa')
            ->with('user', $user)
            ->with('mfa_shared', $secret)
            ->with('image', $image);
    }
}
