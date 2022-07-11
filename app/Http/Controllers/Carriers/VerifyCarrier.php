<?php

namespace App\Http\Controllers\Carriers;

use App\Drivers\DriverFactory;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VerifyCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request): RedirectResponse | View
    {
        $validation = [
            'carrier_api' => [
                'required',
                Rule::in(array_keys(DriverFactory::$supportedDrivers)),
            ],
        ];

        $this->validate($request, $validation);

        try {
            $driverFactory = new DriverFactory($request->input('carrier_api'));
            $driver = $driverFactory->loadDriver();
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Unable to instantiate driver for carrier.']);
        }

        return $driver->verifyCarrierValidation($request);
    }
}
