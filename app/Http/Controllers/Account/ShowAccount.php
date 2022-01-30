<?php

namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ShowAccount extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        return view('account.show' )->with('user', Auth::user() );
    }
}
