<?php

namespace App\Mail;

use App\Jobs\LogEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class SendWelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $password;

    public $email;

    public $host;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $password)
    {
        $this->onQueue('email');
        $this->email = $email;
        $this->password = $password;
        $this->host = config('app.url');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        LogEvent::dispatch(
            'Welcome email sent',
            get_class($this), 'info', json_encode(['recipient' => $this->email]), Auth::user()->id ?? null
        );

        return $this->subject('Account created for you at '.config('app.name'))
            ->markdown('emails.welcome');
    }
}
