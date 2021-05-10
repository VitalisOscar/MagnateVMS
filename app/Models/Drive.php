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
        'driver_out_id',
        'driver_in_id',
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

    function driver_in_(){

    }
}
