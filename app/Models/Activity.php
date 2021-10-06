<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    const TYPE_CHECK_IN = 'Check In';
    const TYPE_CHECK_OUT = 'Leaving';

    const BY_STAFF = 'Staff';
    const BY_VISITOR = 'Visitor';
    const BY_COMPANY_VEHICLE = 'Vehicle';

    protected $fillable = [
        'time', 'type', 'site_id', 'by_id', 'by_type', 'user_id', 'vehicle_id'
    ];

    public $timestamps = false;

    protected $casts = [
        'time' => 'datetime'
    ];

    protected $appends = ['fmt_time', 'fmt_date'];

    // Relations
    function user(){ return $this->belongsTo(User::class); }

    function site(){ return $this->belongsTo(Site::class); }

    function vehicle(){ return $this->belongsTo(Vehicle::class); }

    function by(){ return $this->morphTo('by'); }

    function visit(){ return $this->hasOne(Visit::class); }

    function driver_task(){ return $this->hasOne(DriverActivity::class); }

    // Only to be used when dealing with staff, ensure the byStaff() scope has been applied
    function staff(){ return $this->hasOne(Staff::class, 'id', 'by_id'); }

    // Scopes
    function scopeCheckIn($q){ $q->whereType(self::TYPE_CHECK_IN); }

    function scopeCheckOut($q){ $q->whereType(self::TYPE_CHECK_OUT); }

    function scopeByCompanyVehicle($q){ $q->where('by_type', self::BY_COMPANY_VEHICLE); }

    function scopeByStaff($q){ $q->where('by_type', self::BY_STAFF); }

    function scopeByVisitor($q){ $q->where('by_type', self::BY_VISITOR); }

    function scopeAtSite($q,$site_id){ $q->where('site_id', $site_id); }

    function scopeOnDate($q, $d){
        $q->whereDate('time', $d->format('Y-m-d'));
    }

    // Attributes
    function getFmtDateAttribute(){
        return $this->time->day.' '.substr($this->time->monthName, 0, 3).' '.$this->time->year;
    }

    function getFmtTimeAttribute(){
        if(is_string($this->time)){
            $d = Carbon::createFromTimeString($this->time);
        }else if($this->time == null){
            $d = Carbon::now();
        }else{
            $d = $this->time;
        }

        return $d->format('H:i');
    }

    function getFmtDatetimeAttribute(){
        return $this->fmt_date.' at '.$this->fmt_time;
    }

    function getStaffAttribute(){
        if($this->isByStaff()){
            return $this->relationLoaded('by') ? $this->by : null;
        }
    }

    function getVisitorAttribute(){
        if($this->isByVisitor()){
            return $this->relationLoaded('by') ? $this->by : null;
        }
    }

    function isByStaff(){ return $this->by_type == self::BY_STAFF; }

    function isByVisitor(){ return $this->by_type == self::BY_VISITOR; }

    function isByCompanyVehicle(){ return $this->by_type == self::BY_COMPANY_VEHICLE; }
}
