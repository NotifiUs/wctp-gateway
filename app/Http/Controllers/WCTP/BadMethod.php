<?php

namespace App\Http\Controllers\WCTP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class BadMethod extends Controller
{
    public function __invoke(): Response
    {
        $code = 300;
        $text = 'Operation not supported';
        $desc = 'The requested WCTP operation is not supported by this system. (hint: try HTTP POST)';

        return response()->view('WCTP.wctp-Failure', [
            'errorCode' => $code,
            'errorText' => $text,
            'errorDesc' => $desc,
        ], 200, ['content-type' => 'application/xml']);
    }
}
