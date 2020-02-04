<?php

namespace App\Http\Controllers\SMS;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatusHandler extends Controller
{
    public function __invoke(Request $request)
    {
        return response('<Response></Response>', 200, ['content-type' => 'application/xml']);
    }
}
