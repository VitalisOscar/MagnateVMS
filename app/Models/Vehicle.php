<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'registration_no',
        'description',
        'vehicleable_id',
        'vehicleable_type',
        'slug',
    ];

    function vehicleable(){
        return $this->morphTo();
    }

    function drives(){
        return $this->hasMany(Drive::class);
    }

    function last_drive(){
        return $this->hasOne(Drive::class)->latest('time_out');
    }

    function scopeCompanyOwned($q){
        $q->where('vehicleable_type', null);
    }

    function isCompanyVehicle(){
        return $this->vehicleable_type == null && $this->vehicleable_id == null;
    }

    function isOut(){
        return $this->has('last_drive', function($q){
            $q->stillOut();
        });
    }
}
