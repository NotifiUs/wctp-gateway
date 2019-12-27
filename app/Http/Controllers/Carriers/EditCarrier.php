<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EditCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, Carrier $carrier )
    {
        $this->validate( $request, [
            'name' => "required|string|min:2|max:255|unique:carriers,name,{$carrier->id}",
            'priority' => 'required|integer',
        ]);

        $carrier->name = $request->input('name');
        $carrier->priority = $request->input('priority');

        try{ $carrier->save(); }catch( Exception $e ){ return redirect()->back()->withInput()->withErrors([__('Unable to update carrier')]); }

        $statusHtml = "Carrier successfully updated!";
        return redirect()->to('/carriers')
            ->with('status', $statusHtml);
    }
}
