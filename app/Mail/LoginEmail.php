<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoginEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $host;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( )
    {
        $this->onQueue('email');
        $this->host = config('app.url');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('User login at ' . config('app.name') )
            ->markdown('emails.user_login');
    }
}
