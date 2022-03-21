<?php

namespace App\Http\Controllers\Messages;

use Exception;
use Carbon\Carbon;
use App\Models\Carrier;
use App\Models\Message;
use App\Drivers\DriverFactory;
use App\Models\EnterpriseHost;
use App\Jobs\SubmitToEnterpriseHost;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProcessMessage extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Message $message )
    {
        $carrier = Carrier::find( $message->carrier_id );
        if(  $carrier === null ){ return redirect()->back()->withErrors(['No carrier found']);}

        $host = EnterpriseHost::find( $message->enterprise_host_id );
        if( $host === null ){ return redirect()->back()->withErrors(['No enterprise host found']);}

        try{
            $driverFactory = new DriverFactory( $carrier->api );
            $driver = $driverFactory->loadDriver();
        }
        catch( Exception $e ) {
           return redirect()->back()->withErrors(['Unable to load driver for carrier']);
        }

        if( $message->direction == 'outbound' )
        {
            try{
                $driver->queueOutbound( $host, $carrier, $message->to, decrypt($message->message), $message->messageID, $message->reply_with  );
            }
            catch(Exception $e){
                return redirect()->back()->withErrors(['Unable to queue message for carrier']);
            }
        }
        else
        {
            // reprocess inbound message
            // we don't care about logging this as a message so we don't recreate a message
            $message->delivered_at = null;
            $message->failed_at = null;
            $message->submitted_at = Carbon::now( Auth::user()->timezone );
            $message->processed_at = null;
            $message->status = 'pending';
            $message->save();

            SubmitToEnterpriseHost::dispatch( $message );
        }

        return redirect()->back()->withStatus('Message has been re-processed!');
    }
}
