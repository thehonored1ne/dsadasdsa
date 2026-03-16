<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'expertise_areas',
        'max_units',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function getTotalAssignedUnitsAttribute()
    {
        return $this->assignments()->sum('total_units');
    }

    public function isOverloaded()
    {
        return $this->total_assigned_units > $this->max_units;
    }
}