<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'id_number',
        'id_photo',
        'phone',
        'from'
    ];

    public $timestamps = false;

    function visits(){
        return $this->hasMany(Visit::class);
    }

    function checkedin(){
        return $this->hasMany(Visit::class)->today()->stillIn()->atSite();
    }

    function checkedout(){
        return $this->hasMany(Visit::class)->today()->atSite()->gone();
    }


    function vehicles(){
        return $this->morphMany(Vehicle::class, 'vehicleable');
    }

    function last_visit(){
        // For check out purpose
        // Visitor must not have checked out, and user checking them out must be on same site

        return $this->hasOne(Visit::class)->today()->latest('time_in');
    }

    function any_last_visit(){
        return $this->hasOne(Visit::class)->latest('time_in');
    }

    function can(){
        // is user already checked in?
        $last_visit = $this->visits()->latest('time_in')->first();
        return $last_visit;
    }

    function canCheckIn(){
        // Users who are already checked in at a site cannot
        $visit = $this->visits()
            ->latest('time_in')
            ->atSite()
            ->stillIn()
            ->today()
            ->first();

        // true means they are not checked in at the site
        return $visit == null;
    }
}
