<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Drivers\DriverFactory;
use App\Http\Controllers\Controller;

class CreateCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request )
    {
        $validation = [
            'name' => 'required|string|min:2|max:255|unique:carriers,name',
            'priority' => 'required|integer|unique:carriers,priority',
            'carrier_api' => [
                'required',
                Rule::in(array_keys(DriverFactory::$supportedDrivers))
            ]
        ];

        $this->validate( $request, $validation );

        try{
            $driverFactory = new DriverFactory( $request->input('carrier_api') );
            $driver = $driverFactory->loadDriver();
        }
        catch( Exception $e )
        {
            return redirect()->to('/carriers')->withErrors(['Unable to instantiate driver for carrier.']);
        }

        return $driver->createCarrierInstance($request);
    }
}
