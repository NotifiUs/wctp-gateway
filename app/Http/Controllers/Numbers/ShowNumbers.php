<?php

namespace App\Http\Controllers\Numbers;

use Exception;
use App\Number;
use App\Carrier;
use Twilio\Rest\Client;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\RequestException;

class ShowNumbers extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request, $available = null )
    {
        $active = Number::all()->toArray();

        return view('numbers.show')->with('active', $active );
    }
}
