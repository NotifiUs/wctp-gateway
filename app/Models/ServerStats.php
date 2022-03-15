<?php

namespace App\Models;

use App\Models\Version;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class ServerStats
{
    public static function advanced()
    {
        $advanced = Redis::get('advanced_stats');
        if ($advanced === null) {
            $advanced['load'] = self::getLoad();
            $advanced['uptime'] = self::getUptime();
            $advanced['version'] = self::getVersion();
            $advanced['services'] = self::getServices();
            $advanced['appversion'] = Version::get();

            Redis::setex('advanced_stats', 10, json_encode($advanced));
        } else {
            $advanced = json_decode($advanced, true);
        }

        return $advanced;
    }

    public static function get()
    {
        $server = Redis::get('server_stats');

        if ($server === null) {
            $server['hostname'] = self::getHostname();
            $server['ip'] = self::getIpAddress();
            $server['cpu'] = self::getCPU();
            $server['memory'] = self::getRAM();
            $server['disk'] = self::getDisk();

            Redis::setex('server_stats', 3600, json_encode($server));
        } else {
            $server = json_decode($server, true);
        }

        return $server;
    }

    private static function getVersion()
    {
        return exec("uname -srm 2>&1");
    }

    private static function getServices()
    {
        $php_version = substr(phpversion(), 0, 3);

        $services['nginx'] = ['active' => false, 'icon' => 'fas fa-globe', 'desc' => 'web-server'];
        $services["php{$php_version}-fpm"] = ['active' => false, 'icon' => 'fab fa-php', 'desc' => 'script-preprocessor'];
        $services['redis-server'] = ['active' => false, 'icon' => 'fas fa-hockey-puck', 'desc' => 'memory-store'];
        $services['mysql'] = ['active' => false, 'icon' => 'fas fa-database', 'desc' => 'database-server'];
        $services['supervisor'] = ['active' => false, 'icon' => 'fas fa-user-tie', 'desc' => 'process-watcher'];

        foreach ($services as $service => $details) {
            $output = '';
            $return_value = null;
            $status = exec("service {$service} status 2>&1", $output, $return_value);
            $services[$service]['desc'] = implode("\n", $output);
            if ($return_value === 0 && $status) {
                $services[$service]['status'] = true;
            }
            $details = null;
        }

        return $services;
    }

    private static function getLoad()
    {
        $load = exec("uptime");
        $load = explode('load average:', $load);
        if (is_array($load)) {
            return $load[1];
        }

        return 'Error getting info';

    }

    private static function getUptime()
    {
        $uptime = File::get('/proc/uptime', false);
        $uptime = explode(' ', trim($uptime));
        $uptime_in_seconds = $uptime[0];

        return Carbon::now()->subMilliseconds($uptime_in_seconds * 1000)
            ->diffForHumans(null, CarbonInterface::DIFF_ABSOLUTE, true, 3);
    }

    private static function getHostname()
    {
        return gethostname() ?? 'localhost';
    }

    private static function getIpAddress()
    {
        return Request::server('SERVER_ADDR') ?? '127.0.0.1';
    }

    private static function getCPU()
    {
        $cpu_report = fopen('/proc/cpuinfo', 'r');
        $cpu = '';
        while ($line = fgets($cpu_report)) {
            $pieces = array();
            if (Str::contains($line, 'cpu MHz')) {
                $pieces = explode(':', $line);
                $cpu = trim($pieces[1]);
                break;
            }
        }
        fclose($cpu_report);
        $cpu = round($cpu / 1024, 2);
        return "{$cpu} GHz";
    }

    private static function getRAM()
    {
        $memory_report = fopen('/proc/meminfo', 'r');
        $mem = 0;
        while ($line = fgets($memory_report)) {
            $pieces = array();
            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                $mem = $pieces[1];
                break;
            }
        }
        fclose($memory_report);
        $mem = round($mem / 1024 / 1024, 2);
        return "{$mem} GB";
    }

    private static function getDisk()
    {
        $free_disk_space_gb = round(disk_free_space('/') / 1024 / 1024 / 1024, 2);
        $total_disk_space_gb = round(disk_total_space('/') / 1024 / 1024 / 1024, 2);
        $used_disk_space_gb = round($total_disk_space_gb - $free_disk_space_gb, 2);
        //$disk_free_percent = round( ( $free_disk_space_gb / $total_disk_space_gb ) * 100, 2);
        $used_disk_percent = round(($used_disk_space_gb / $total_disk_space_gb) * 100, 0);

        return [
            'value' => $used_disk_space_gb,
            'percent' => $used_disk_percent,
            'total' => $total_disk_space_gb
        ];
    }
}
