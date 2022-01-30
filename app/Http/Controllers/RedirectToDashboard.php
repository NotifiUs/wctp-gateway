<?php

namespace App\Http\Controllers;

class RedirectToDashboard extends Controller
{
    public function __invoke()
    {
        return redirect()->to('home');
    }
}
