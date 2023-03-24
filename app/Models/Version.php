<?php

namespace App\Models;

use Carbon\Carbon;

class Version
{
    const MAJOR = 1;

    const MINOR = 12;

    const PATCH = 3;

    public static function get()
    {
        $commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));

        $commitDate = Carbon::parse(trim(exec('git log -n1 --pretty=%ci HEAD')), 'UTC');

        return 'wctpd '.sprintf('v%s.%s.%s.%s (%s)', self::MAJOR, self::MINOR, self::PATCH, $commitHash, $commitDate->format('Y-m-d H:i:s'));
    }
}
