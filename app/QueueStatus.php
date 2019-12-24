<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class QueueStatus
{
    public static function get()
    {
        $queue = Redis::get('queue_status');

        if( is_null( $queue ) )
        {
            $queue = 0;
            $artisan = base_path('artisan') ;
            $horizon = exec("php {$artisan} horizon:status");

            if( Str::contains( $horizon, 'running' ) )
            {
                $queue = 1;
            }

            Redis::setex('queue_status', 60, $queue);
        }

        return $queue;
    }
}
