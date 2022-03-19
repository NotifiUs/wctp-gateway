<?php

namespace App\Jobs;

use App\Models\User;
use Throwable;
use Exception;
use App\Models\Carrier;
use App\Models\Message;
use Carbon\Carbon;
use App\Mail\FailedJob;
use Illuminate\Bus\Queueable;
use App\Drivers\DriverFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncOutboundStatus implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $driver;
    public int $tries = 10;
    public int $timeout = 60;
    public int $uniqueFor = 3600;
    public Message|null $message;
    public Carrier|null $carrier;
    public bool $failOnTimeout = true;
    public bool $deleteWhenMissingModels = true;

    public function __construct( Message $message )
    {
        $this->onQueue('outbound');
        $this->message = $message;
    }

    public function handle()
    {
        $this->carrier = Carrier::where( 'enabled', 1 )->where( 'id', $this->message->carrier_id )->first();

        if( $this->carrier === null )
        {
            LogEvent::dispatch(
                "Failure synchronizing message status",
                get_class( $this ), 'error', json_encode("No enabled carrier for message"), null
            );

            $this->release(60);
        }

        try{
            $driverFactory = new DriverFactory( $this->carrier->api );
            $this->driver = $driverFactory->loadDriver();
        }
        catch( Exception $e ) {
            LogEvent::dispatch(
                "Failure synchronizing message status",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );

            $this->release(60);
        }

        if( $this->driver->updateMessageStatus( $this->carrier, $this->message ) === false )
        {
            $this->release(60);
        }

        return 0;
    }

    public function uniqueId()
    {
        return $this->message->id;
    }

    public function failed(Throwable $exception )
    {

        foreach (User::where('email_notifications', true)->get() as $u) {
            try{
                Mail::to($u->email)->queue(new FailedJob($this->message->toArray()));
            }
            catch( Exception $e )
            {
                LogEvent::dispatch(
                    "Unable to send failed jobs notifications",
                    get_class($this), 'error', json_encode([$e->getMessage(), $u->email]), null
                );
            }
        }

        LogEvent::dispatch(
            "SyncOutboundStatus Job failed",
            get_class($this), 'error', json_encode($exception->getMessage()), null
        );

        try {
            $this->message->status = 'failed';
            $this->message->failed_at = Carbon::now();
            $this->message->save();
        } catch (Exception $e) {
            LogEvent::dispatch(
                "Job status update failed",
                get_class($this), 'error', json_encode($e->getMessage()), null
            );
        }
    }
}
