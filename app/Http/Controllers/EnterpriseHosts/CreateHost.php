<?php

namespace App\Http\Controllers\EnterpriseHosts;

use App\Jobs\LogEvent;
use Faker;
use Exception;
use App\EnterpriseHost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use FakerProviderAnimals\Animals;
use Illuminate\Support\Facades\Validator;

class CreateHost extends Controller
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

        $faker = Faker\Factory::create();
        $faker->addProvider(new Animals($faker));
        $senderID = Str::slug( "{$faker->safeColorName} {$faker->animal()}" );
        $host_type = 'amtelco';
        $enabled = 0;
        try{
            $securityCode = bin2hex( random_bytes( 8 ) );
        }catch( Exception $e ){ return redirect()->back()->withInput()->withErrors([$e->getMessage()]); }

        $validator = Validator::make([
            'senderID' => $senderID,
            'securityCode' => $securityCode,
            'type' => $host_type,
            'enabled'=> $enabled,
        ], [
            'senderID' => 'required|string|alpha_dash|unique:enterprise_hosts,senderID|max:128',
            'securityCode' => 'required|string|max:16',
            'type' => 'required|string|in:amtelco',
            'enabled'=> 'required|boolean|in:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $eh = new EnterpriseHost;
        $eh->name = $request->input('name');
        $eh->senderID = $senderID;
        $eh->securityCode = encrypt( $securityCode );
        $eh->type = $host_type;
        $eh->url = $request->input('url');
        $eh->enabled = $enabled;

        try{ $eh->save(); }catch( Exception $e ){ return redirect()->back()->withInput()->withErrors([__('Unable to save host')]); }

        LogEvent::dispatch(
            "{$eh->senderID} setup",
            get_class( $this ), 'info', json_encode($eh->toArray()), Auth::user()->id ?? null
        );
        $statusHtml = "Enterprise host created!<br><br><strong>senderID</strong>: {$senderID}<br><strong>securityCode</strong>: <code class=\"text-success\">{$securityCode}</code>";
        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
