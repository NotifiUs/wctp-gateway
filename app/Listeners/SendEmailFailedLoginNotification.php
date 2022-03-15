<?php

namespace App\Listeners;

use App\Models\User;
use App\Mail\FailedLoginEmail;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailFailedLoginNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        Mail::to( $event->credentials['email'] )->send(new FailedLoginEmail());
    }

    public function shouldQueue( Failed $event )
    {
        $user = User::where('email', $event->credentials['email'] )->first();

        if( $user === null )
        {
            return false;
        }

        return true;
    }
}
