<?php

namespace App\Mail;

use App\Jobs\LogEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /*LogEvent::dispatch(
            "Welcome email sent",
            get_class( $this ), 'info',json_encode( ['recipient' => $this->email]) , Auth::user()->id ?? null
        );*/

        return $this->subject('Password reset at ' . config('app.name') )
            ->markdown('emails.password_reset');
    }
}
