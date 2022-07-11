<?php

namespace App\Jobs;

use App\Models\EventLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;

    public int $timeout = 60;

    public bool $failOnTimeout = true;

    protected $event;

    protected $source;

    protected $severity;

    protected $details;

    protected $user_id;

    public function __construct($event, $source, $severity = 'info', $details = null, $user_id = null)
    {
        $this->onQueue('default');
        $this->event = $event;
        $this->source = $source;
        $this->severity = $severity;
        $this->details = $details;
        $this->user_id = $user_id;
    }

    public function handle()
    {
        $event = new EventLog;
        $event->event = $this->event;
        $event->source = str_replace(['App\\Http\\Controllers\\', 'App\\'], '', $this->source);
        $event->severity = $this->severity;
        $event->details = $this->details;
        $event->user_id = $this->user_id;

        try {
            $event->save();
        } catch (Exception $e) {
            $this->release(60);
        }
    }
}
