<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    public $fillable = [
        'checked_in_by',
        'visitor_id',
        'reason',
        'staff_id',
        'date',
        'time_in',
        'time_out',
	    'items_in',
	'site_id',
        'car_registration',
        'from', // company from
        'card_number',
        'signature'
    ];

    public $timestamps = false;

    function wasToday(){
	$in = Carbon::createFromTimeString($this->time_in);
	return $in->isToday();
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

    function visitor(){
        return $this->belongsTo(Visitor::class);
    }

    function checked_out(){
        return $this->time_out != null;
    }

    function scopeUnCheckedOut($q){
        $q->where('time_out', null);
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
