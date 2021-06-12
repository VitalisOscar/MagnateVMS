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
        'vehicle_id',
    ];

    public $timestamps = false;

    public $with = ['vehicle'];

    public $appends = ['car_registration'];

    function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

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


    function getFmtDateAttribute(){
        $t = Carbon::createFromTimeString($this->time_in);

        if($t->isToday()){
            return 'Today';
        }else if($t->isYesterday()){
            return 'Yesterday';
        }

        if($t->isCurrentYear())
        return substr($t->monthName, 0, 3).' '.$t->day;

        return substr($t->monthName, 0, 3).' '.$t->day.', '.$t->year;
    }

    function getDateAttribute(){
        return Carbon::createFromTimeString($this->time_in)->format('Y-m-d');
    }

    function getCheckInAttribute(){
        $u = $this->check_in_user;
        return Carbon::createFromTimeString($this->time_in)->format('H:i').' by '.$u->name;
    }

    function getCheckOutAttribute(){
        if(Carbon::createFromTimeString($this->time_in)->isToday() && $this->check_out_user == null){
            return 'Still In';
        }else{
            return 'Not Captured';
        }

        $u = $this->check_out_user;
        return Carbon::createFromTimeString($this->time_out)->format('H:i').' by '.$u->name;
    }

    function getCarRegistrationAttribute(){
        $v = $this->vehicle;
        return $v ? $v->registration_no:null;
    }
}
