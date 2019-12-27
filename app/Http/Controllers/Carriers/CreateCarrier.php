<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreateCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request )
    {
        $this->validate( $request, [
            'name' => 'required|string|min:2|max:255|unique:enterprise_hosts,name',
            'url' => 'required|url|starts_with:https',
        ]);

        $carrier = new Carrier;
        $carrier->name = $request->input('name');
        $carrier->enabled = 0;

        try{ $carrier->save(); }catch( Exception $e ){ return redirect()->back()->withInput()->withErrors([__('Unable to save carrier')]); }

        $statusHtml = "Carrier successfully created!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
