<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Jobs\LogEvent;
use App\Models\Carrier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditCarrier extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request, Carrier $carrier)
    {
        $this->validate($request, [
            'name' => "required|string|min:2|max:255|unique:carriers,name,{$carrier->id}",
            'priority' => "required|integer|unique:carriers,priority,{$carrier->id}",
        ]);

        $carrier->name = $request->input('name');
        $carrier->priority = $request->input('priority');

        try {
            $carrier->save();
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors([__('Unable to update carrier')]);
        }

        LogEvent::dispatch(
            "{$carrier->name} ({$carrier->api}) updated",
            get_class($this), 'info', json_encode($carrier->toArray()), Auth::user()->id ?? null
        );
        $statusHtml = 'Carrier successfully updated!';

        return redirect()->to('/carriers')
            ->with('status', $statusHtml);
    }
}
