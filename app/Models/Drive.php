<?php

namespace App\Models;

use Carbon\Carbon;
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

    function check_in_user(){
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    function check_out_user(){
        return $this->belongsTo(User::class, 'checked_out_by');
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

    function getCheckInAttribute(){
        if($this->check_in_user == null){
            return 'Not Captured';
        }

        $in = Carbon::createFromTimeString($this->time_in);

        $u = $this->check_in_user;
        return $in->format('Y-m-d H:i').' (by '.$u->name.')';
    }

    function getCheckOutAttribute(){
        if($this->check_out_user == null){
            return 'Not Captured';
        }

        $out = Carbon::createFromTimeString($this->time_out);

        $u = $this->check_out_user;
        return $out->format('Y-m-d H:i').' (by '.$u->name.')';
    }
}
