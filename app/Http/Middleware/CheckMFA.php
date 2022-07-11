<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckMFA
{
    protected $except = [
        'mfa',
        'login',
        'logout',
        '/password/*',
        '/wctp',
        '/sms/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->inExceptArray($request)) {
            if (Auth::check()) {
                if (Auth::user()->mfa_secret && ! Session::exists('mfa_valid')) {
                    //only redirect if we aren't on the mfa page
                    if ($request->path() !== 'mfa') {
                        return redirect()->to('/mfa');
                    }
                }
            }
        }

        return $next($request);
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
