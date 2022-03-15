<?php

namespace App\Http\Controllers\Messages;

use Exception;
use App\Models\Carrier;
use App\Models\Message;
use Carbon\Carbon;
use App\Models\EnterpriseHost;
use App\Jobs\SendThinqSMS;
use App\Jobs\SendTwilioSMS;
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

        if( $message->direction == 'outbound' )
        {
            if( $carrier->api == 'twilio' )
            {
                try{
                    SendTwilioSMS::dispatch( $host, $carrier, substr($message->to, 2), decrypt($message->message), $message->messageID, $message->reply_with );
                }
                catch( Exception $e )
                {
                    return redirect()->back()->withErrors([$e->getMessage()]);
                }
            }
            elseif( $carrier->api == 'thinq' )
            {
                try{
                    SendThinqSMS::dispatch( $host, $carrier, substr($message->to, 2), decrypt($message->message), $message->messageID, $message->reply_with );
                }
                catch( Exception $e )
                {
                    return redirect()->back()->withErrors([$e->getMessage()]);
                }
            }

            return redirect()->back()->withErrors(['Carrier api not supported']);
        }

        // reprocess inbound message
        // we don't care about logging this as a message so we don't recreate a message
        $message->delivered_at = null;
        $message->failed_at = null;
        $message->submitted_at = Carbon::now( Auth::user()->timezone );
        $message->processed_at = null;
        $message->status = 'pending';
        $message->save();

        SubmitToEnterpriseHost::dispatch( $message );

        return redirect()->back()->withStatus('Message has been re-processed!');
    }
}
