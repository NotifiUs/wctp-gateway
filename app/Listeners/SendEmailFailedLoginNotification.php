<?php

namespace App\Listeners;

use App\Mail\FailedLoginEmail;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

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
        Mail::to($event->credentials['email'])->send(new FailedLoginEmail());
    }

    public function shouldQueue(Failed $event)
    {
        $user = User::where('email', $event->credentials['email'])->first();

        if ($user === null) {
            return false;
        }

        return true;
    }
}
