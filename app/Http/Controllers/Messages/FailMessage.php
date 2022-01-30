<?php

namespace App\Http\Controllers\Messages;

use App\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class FailMessage extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Message $message )
    {

            // fail the message
            $message->delivered_at = null;
            $message->failed_at = Carbon::now( Auth::user()->timezone );
            $message->status = 'failed';
            $message->save();

        return redirect()->back()->withStatus('Message has been re-processed!');
    }
}
