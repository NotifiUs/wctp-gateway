<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RetryJob extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $details;

    public $host;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $details = [])
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
        return $this->markdown('emails.retry_job')->subject('WCTP job retried at '.config('app.name'));
    }
}
