<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffCheckIn extends Model
{
    use HasFactory;

    public $table = "staff_check_ins";

    public $fillable = [
        'checked_in_by',
        'staff_id',
        'time_in',
        'time_out',
        'site_id',
        'car_registration',
    ];

    public $timestamps = false;

    function check_in_user(){
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    function check_out_user(){
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    function staff(){
        return $this->belongsTo(Staff::class);
    }

    function site(){
        return $this->belongsTo(Site::class);
    }

    function checked_out(){
        return $this->time_out != null;
    }

    function scopeStillIn($q){
        $q->where('time_out', null);
    }

    function scopeAtSite($q){
        $q->where('site_id', auth('sanctum')->user()->site_id);
    }

    function scopeToday($q){
        $q->whereDate('time_in', Carbon::today()->format('Y-m-d'));
    }

}
