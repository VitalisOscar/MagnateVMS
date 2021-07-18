<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "staff";

    public $timestamps = false;

    public $fillable = [
        'company_id',
        'name',
        'phone',
        'email'
    ];

    function company(){
        return $this->belongsTo(Company::class);
    }

    function visits(){
        return $this->hasMany(Visit::class);
    }

    function check_ins(){
        return $this->hasMany(StaffCheckIn::class);
    }

    function vehicles(){
        return $this->morphMany(Vehicle::class, 'vehicleable');
    }

    function isCheckedIn(){
        return $this->check_ins()->latest('time_in')->stillIn()->atSite()->today()->first() != null;
    }
}
