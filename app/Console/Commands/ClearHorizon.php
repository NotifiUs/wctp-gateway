<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ClearHorizon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horizon:wipe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs queue:flush and purges all horizon failed jobs and keys in redis';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $prefix = config('horizon.prefix');

        /* https://github.com/laravel/horizon/issues/122 */

        $this->info("");
        $this->info("Running queue:flush...");
        $this->call('queue:flush');

        $this->info("");
        $this->info("Deleting {$prefix}failed:*...");
        Redis::connection()->del(["{$prefix}failed:*"]);

        $this->info("");
        $this->info("Deleting {$prefix}failed_jobs:*...");
        Redis::connection()->del(["{$prefix}failed_jobs"]);

        $this->info("");
        $this->alert('Complete!');
    }
}
