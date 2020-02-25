<?php

namespace App\Providers;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::failing(function ( JobFailed $event ) {
            // $event->connectionName
            // $event->job
            // $event->exception
            // failover support
            // get carrier of job
            // see if there is another carrier (log carrier switch)
            // if so, create a job to send the message on that carrier
            // if not, log a failure and notify team
        });
    }
}
