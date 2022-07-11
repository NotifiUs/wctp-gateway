<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $dates = [
        'submitted_at',
        'processed_at',
        'delivered_at',
        'failed_at',
    ];

    protected $hidden = [
        'message',
    ];

    public function carrier()
    {
        return $this->belongsTo('App\Models\Carrier');
    }

    public function number()
    {
        return $this->belongsTo('App\Models\Number');
    }
}
