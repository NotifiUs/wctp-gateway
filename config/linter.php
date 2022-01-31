<?php

/*
 * Customize the Vale styles used by the linter.
 */

use Beyondcode\LaravelProseLinter\Styles\Vale;
use Beyondcode\LaravelProseLinter\Styles\WriteGood;

return [
    'styles' => [
        WriteGood::class,
        Vale::class,
    ],

];
