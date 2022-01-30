<?php

namespace App\Http\Controllers\EnterpriseHosts;

use Exception;
use App\Jobs\LogEvent;
use App\EnterpriseHost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

        LogEvent::dispatch(
            "{$host->senderID} updated",
            get_class( $this ), 'info', json_encode($host->toArray()), Auth::user()->id ?? null
        );
        $statusHtml = "Enterprise host updated!";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
