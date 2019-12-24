<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;


class ServerStats
{
    public static function get()
    {
        $server = Redis::get('server_stats');

        if( is_null( $server ) )
        {
            $server['hostname'] =   self::getHostname();
            $server['ip'] =         self::getIpAddress();
            $server['cpu'] =        self::getCPU();
            $server['memory'] =     self::getRAM();
            $server['disk'] =       self::getDisk();

            Redis::setex( 'server_stats', 3600,  json_encode( $server ) );
        }
        else
        {
            $server = json_decode( $server, true );
        }

        return $server;
    }

    private static function getHostname()
    {
        return gethostname() ?? 'localhost';
    }

    private static function getIpAddress()
    {
        return Request::server( 'SERVER_ADDR' ) ?? '127.0.0.1';
    }

    private static function getCPU()
    {
        $cpu_report = fopen('/proc/cpuinfo', 'r' );
        $cpu = '';
        while ( $line = fgets( $cpu_report ) )
        {
            $pieces = array();
            if( Str::contains( $line, 'cpu MHz') )
            {
                $pieces = explode( ':', $line );
                $cpu = trim( $pieces[1] );
                break;
            }
        }
        fclose( $cpu_report );
        $cpu = round($cpu / 1024, 2);
        return "{$cpu} GHz";
    }

    private static function getRAM()
    {
        $memory_report = fopen('/proc/meminfo', 'r' );
        $mem = 0;
        while ( $line = fgets( $memory_report ) )
        {
            $pieces = array();
            if ( preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces) ) {
                $mem = $pieces[1];
                break;
            }
        }
        fclose( $memory_report );
        $mem = round($mem / 1024 / 1024, 2);
        return "{$mem} GB";
    }

    private static function getDisk()
    {
        $free_disk_space_gb = round( disk_free_space('/') / 1024 / 1024 / 1024, 2);
        $total_disk_space_gb = round( disk_total_space('/') / 1024 / 1024 / 1024, 2);
        $used_disk_space_gb = round( $total_disk_space_gb - $free_disk_space_gb, 2);
        //$disk_free_percent = round( ( $free_disk_space_gb / $total_disk_space_gb ) * 100, 2);
        $used_disk_percent = round( ( $used_disk_space_gb / $total_disk_space_gb ) * 100, 0 );

        return [
            'value' => $used_disk_space_gb,
            'percent' => $used_disk_percent,
            'total' => $total_disk_space_gb
        ];
    }
}
