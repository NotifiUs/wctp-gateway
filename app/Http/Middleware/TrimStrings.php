<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
        //https://www.twilio.com/docs/usage/security#validating-requests
        // See *A Few Notes* section on RequestValidator failures from trailing whitespaces being trimmed
        // Thanks Alex @ i24 for finding this bug (and confirming the fix didnt work without the capital B)
        'Body',
    ];
}
