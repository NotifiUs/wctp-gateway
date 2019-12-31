<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectToDashboard extends Controller
{
    public function __invoke(Request $request)
    {
        return redirect()->to('home');
    }
}
