<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FailedJob extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $details, $host;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( array $details = [] )
    {
        $this->onQueue('email');
        $this->details = $details;
        $this->host = config('app.url');

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.failed_job')->subject('Failed WCTP job at ' . config('app.name') );
    }
}
