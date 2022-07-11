<?php

namespace App\Models;

use function base_path;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class QueueStatus
{
    public static function get()
    {
        $queue = Redis::get('queue_status');

        if ($queue === null) {
            $queue = 0;
            $artisan = base_path('artisan');
            $horizon = exec("php {$artisan} horizon:status");

            if (Str::contains($horizon, 'running')) {
                $queue = 1;
            }

            Redis::setex('queue_status', 60, $queue);
        }

        return $queue;
    }
}
