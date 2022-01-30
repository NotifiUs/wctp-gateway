<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Horizon::routeMailNotificationsTo(config('mail.notifications') );
        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
        Horizon::night();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        //according to documentation, this function omits non-authenticated users (i.e., $user being null never hits this)
        Gate::define('viewHorizon', function ($user) {
            return true;
        });
    }
}
