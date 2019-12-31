<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ShowAccount extends Controller
{
    public function __invoke(Request $request)
    {
        return view('account.show' )->with('user', Auth::user() );
    }
}
