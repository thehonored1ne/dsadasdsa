<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'day',
        'time_start',
        'time_end',
        'room',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}