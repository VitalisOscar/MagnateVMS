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

    function last_visit(){
        // For check out purpose
        // Visitor must not have checked out, and user checking them out must be on same site

        return $this->hasOne(Visit::class)->today();
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
            ->whereHas('staff', function($q1){
                $q1->whereHas('company', function($q2){
                    $q2->where('site_id', auth('sanctum')->user()->site_id);
                });
            }) // seeing staff on same site as app user
            ->where('time_out', null) // still (or suppossed to be) in
            ->whereDate('time_in', Carbon::today()->format('Y-m-d')) // TODO Base on time in be current date
            ->first();

        // true means they are not checked in at the site
        return $visit == null;
    }
}
