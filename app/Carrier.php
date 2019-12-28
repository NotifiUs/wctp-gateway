<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    public function numbers()
    {
        return $this->hasMany('App\Number');
    }
}
