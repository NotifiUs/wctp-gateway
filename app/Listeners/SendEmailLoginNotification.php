<?php

namespace App\Listeners;

use App\Mail\LoginEmail;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailLoginNotification implements ShouldQueue
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
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        Mail::to($event->user['email'])->send(new LoginEmail());
    }

    public function shouldQueue(Login $event)
    {
        $user = User::where('email', $event->user['email'])->first();

        if ($user === null) {
            return false;
        }

        return true;
    }
}
