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

    function site(){
        return $this->belongsTo(Site::class);
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

    function scopeGone($q){
        $q->where('time_out', '<>', null);
    }

    function scopeAtSite($q){
        $q->where('site_id', auth('sanctum')->user()->site_id);
    }

    function scopeToday($q){
        $q->whereDate('time_in', Carbon::today()->format('Y-m-d'));
    }

    function scopeNoCardIssued($q){
        $q->where('card_number', null);
    }

    function scopeCardIssued($q){
        $q->where('card_number', '<>', null);
    }

    function getTimeAttribute(){
        /** @var Carbon */
        $t = Carbon::createFromTimeString($this->time_in);
        $now = Carbon::now();

        if($t->isToday()){
            return 'Today ' .$t->format('H:i');
        }else if($t->isYesterday()){
            return 'Yesterday at ' .$t->format('H:i');
        }

        if($t->isCurrentYear())
        return substr($t->monthName, 0, 3).' '.$t->day.' at '.$t->format('H:i');

        return substr($t->monthName, 0, 3).' '.$t->day.', '.$t->year.' at '.$t->format('H:i');
    }

    function getHostAttribute(){
        $staff = $this->staff;

        if($staff != null){
            return $staff->name.' ('.$staff->company->name.')';
        }
    }

    function fmtTime($t){
        $t = Carbon::createFromTimeString($t);

        if($t->isToday()){
            return 'Today ' .$t->format('H:i');
        }else if($t->isYesterday()){
            return 'Yesterday at ' .$t->format('H:i');
        }

        if($t->isCurrentYear())
        return substr($t->monthName, 0, 3).' '.$t->day.' at '.$t->format('H:i');

        return substr($t->monthName, 0, 3).' '.$t->day.', '.$t->year.' at '.$t->format('H:i');
    }

    function getCheckInAttribute(){
        $u = $this->check_in_user;
        return $this->fmtTime($this->time_in).' by '.$u->name;
    }

    function getCheckOutAttribute(){
        if($this->check_out_user == null){
            return 'Not Captured';
        }

        $u = $this->check_out_user;
        return Carbon::createFromTimeString($this->time_out)->format('H:i').' by '.$u->name;
    }

}
