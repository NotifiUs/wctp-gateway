<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyEmail extends Controller
{
    public function __invoke(Request $request)
    {
        return view('account.verify' );
    }
}
