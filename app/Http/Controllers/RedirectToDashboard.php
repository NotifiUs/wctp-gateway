<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class RedirectToDashboard extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->to('home');
    }
}
