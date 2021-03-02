<?php

namespace App\Jobs;

use Exception;
use App\EventLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LogEvent implements ShouldQueue
{

    public $tries = 10;
    public $timeout = 60;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event, $source, $severity, $details, $user_id;

    public function __construct( $event, $source, $severity = 'info', $details = null, $user_id = null )
    {
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
        $event->source = str_replace(["App\\Http\\Controllers\\","App\\"], "", $this->source );
        $event->severity = $this->severity;
        $event->details = $this->details;
        $event->user_id = $this->user_id;

        try{
            $event->save();
        }
        catch( Exception $e ){
            return $this->release(60 );
        }

    }
}
