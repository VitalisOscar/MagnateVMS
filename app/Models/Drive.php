<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drive extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'vehicle_id',
        'driveable_out_id',
        'driveable_out_type',
        'driveable_in_id',
        'driveable_in_type',
        'time_out',
        'time_in',
        'mileage_out',
        'mileage_in',
        'fuel_out',
        'fuel_in',
    ];

    function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

    function driveable_in(){
        return $this->morphTo();
    }

    function driveable_out(){
        return $this->morphTo();
    }

    function scopeStillOut($q){
        $q->where('time_in', null);
    }

    function isCheckedIn(){
        return $this->time_in != null;
    }
}
