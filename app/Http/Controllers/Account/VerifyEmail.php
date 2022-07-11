<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

class VerifyEmail extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        return view('account.verify');
    }
}
