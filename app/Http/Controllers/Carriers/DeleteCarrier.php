<?php

namespace App\Http\Controllers\Carriers;

use Exception;
use App\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, Carrier $carrier )
    {
        try{ $carrier->delete(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to delete carrier')]); }

        $statusHtml = "Carrier successfully deleted!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
