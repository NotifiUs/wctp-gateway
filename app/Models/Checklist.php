<?php

namespace App\Models;

use App\Models\Carrier;
use App\Models\EnterpriseHost;
use Illuminate\Support\Str;

class Checklist
{
    public static function get()
    {
        $carriers = self::hasEnabledCarrier();
        $hosts = self::hasEnabledHost();
        $numbers = self::carriersHaveEnabledNumbers();

        return array_merge( $carriers, $hosts, $numbers );
    }

    protected static function carriersHaveEnabledNumbers()
    {
        $checklist = [];
        foreach( Carrier::where('enabled', 1)->get() as $carrier )
        {
            if( $carrier->numbers->where('enabled', 1)->count() == 0 )
            {
                $checklist[] = [
                    'description' => "No enabled numbers for {$carrier->name}",
                    'link' => '/numbers'
                ];
            }
        }
        return $checklist;
    }


    protected static function hasEnabledCarrier()
    {
        $checklist = [];
        if( Carrier::where('enabled', 1)->count() === 0 )
        {
            $checklist[] =  [
                'description' => 'No enabled Carrier API providers',
                'link' => '/carriers'
            ];
        }
        return $checklist;
    }

    protected static function hasEnabledHost()
    {
        $checklist = [];
        if( EnterpriseHost::where('enabled', 1)->count()  === 0 )
        {
            $checklist[] =  [
                'description' => 'No enabled Enterprise Hosts',
                'link' => '/hosts'
            ];
        }
        return $checklist;
    }
}
