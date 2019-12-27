<?php

namespace App\Http\Controllers\EnterpriseHosts;

use Exception;
use App\EnterpriseHost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EditHost extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke( Request $request, EnterpriseHost $host )
    {
        $this->validate( $request, [
            'name' => "required|string|min:2|max:255|unique:enterprise_hosts,name,{$host->id}",
            'url' => 'required|url|starts_with:https',
        ]);

        $host->name = $request->input('name');
        $host->url = $request->input('url');

        try{ $host->save(); }catch( Exception $e ){ return redirect()->back()->withErrors([__('Unable to update host')]); }

        $statusHtml = "Enterprise host updated!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
