<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    const TYPE_CHECK_IN = 'Check In';
    const TYPE_CHECK_OUT = 'Check Out';

    const BY_STAFF = 'Staff';
    const BY_VISITOR = 'Visitor';
    const BY_COMPANY_VEHICLE = 'Vehicle';

    protected $fillable = [
        'timestamp', 'date', 'time', 'type', 'site_id', 'by_id', 'by_type', 'user_id', 'vehicle_id', 'checkin_activity_id', 'checkout_activity_id'
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected $with = ['user'];

    protected $appends = ['fmt_time', 'fmt_date'];

    protected $keyType = 'string';

    function __construct($data = []){
        parent::__construct($data);

        $this->created_at = $data['timestamp'] ?? null;
        $this->site = new Site($data['site'] ?? []);
        if(isset($data['user'])) $this->user = new User($data['user'] ?? []);
        if(isset($data['vehicle'])) $this->vehicle = new Vehicle($data['vehicle'] ?? []);
        if(isset($data['visit'])) $this->visit = new Visit($data['visit'] ?? []);
        if(isset($data['driver_activity'])) $this->driver_task = new DriverActivity($data['driver_activity'] ?? []);
        if(isset($data['check_in_activity'])) $this->checkin_activity = new Activity($data['check_in_activity']);
        if(isset($data['check_out_activity'])) $this->checkout_activity = new Activity($data['check_out_activity'] ?? []);

        if($this->isByStaff()){
            $this->by = new Staff($data['by'] ?? []);
        }

        if($this->isByVisitor()){
            $this->by = new Visitor($data['by'] ?? []);
        }

        if($this->isByCompanyVehicle()){
            $this->by = new Vehicle($data['by'] ?? []);
        }

        $this->user_id = $data['user']['id'] ?? null;
        $this->site_id = $data['site']['id'] ?? null;
        $this->vehicle_id = $data['vehicle']['id'] ?? null;
        $this->by_id = $data['by']['id'] ?? null;
    }

    // Relations
    function checkin_activity(){
        return $this->hasOne(Activity::class, 'checkin_activity_id')->checkIn();
    }

    function checkout_activity(){
        return $this->hasOne(Activity::class, 'checkout_activity_id')->checkOut();
    }


    function user(){ return $this->belongsTo(User::class); }

    function site(){ return $this->belongsTo(Site::class); }

    function vehicle(){ return $this->belongsTo(Vehicle::class); }

    function by(){ return $this->morphTo('by'); }

    function visit(){ return $this->hasOne(Visit::class); }

    function driver_task(){ return $this->hasOne(DriverActivity::class); }

    // Only to be used when dealing with staff, ensure the byStaff() scope has been applied
    function staff(){ return $this->hasOne(Staff::class, 'id', 'by_id'); }

    // Only to be used when dealing with company vehicles, ensure the byCompanyVehicle() scope has been applied
    function companyVehicle(){ return $this->hasOne(Vehicle::class, 'id', 'by_id')->companyOwned(); }

    // Scopes
    function scopeCheckIn($q){ $q->whereType(self::TYPE_CHECK_IN); }

    function scopeCheckOut($q){ $q->whereType(self::TYPE_CHECK_OUT); }

    function scopeByCompanyVehicle($q){ $q->where('by_type', self::BY_COMPANY_VEHICLE)->whereHas('by'); }

    function scopeByStaff($q){ $q->where('by_type', self::BY_STAFF)->whereHas('by'); }

    function scopeByVisitor($q){ $q->where('by_type', self::BY_VISITOR)->whereHas('by'); }

    function scopeByHuman($q){
        $q->where(function($q1){
            $q1->where('by_type', self::BY_VISITOR)
                ->orWhere('by_type', self::BY_STAFF);
        })->whereHas('by');
    }

    function scopeAtSite($q,$site_id){ $q->where('site_id', $site_id); }

    function scopeOnDate($q, $d){
        $q->whereDate('time', $d->format('Y-m-d'));
    }

    function scopeNotCheckedOut($q){
        $q->where('checkout_activity_id', null);
    }

    function scopeNotCheckedIn($q){
        $q->where('checkin_activity_id', null);
    }

    function scopeCheckedOut($q){
        $q->where('checkout_activity_id', '<>', null);
    }

    // Attributes
    function getFmtDateAttribute(){
        return $this->created_at->day.' '.substr($this->created_at->monthName, 0, 3).' '.$this->created_at->year;
    }

    function getFmtTimeAttribute(){
        if(is_string($this->created_at)){
            $d = Carbon::createFromTimeString($this->created_at);
        }else if($this->created_at == null){
            $d = Carbon::now();
        }else{
            $d = $this->created_at;
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

    function isCheckIn(){
        return $this->type == self::TYPE_CHECK_IN;
    }

    function isCheckOut(){
        return $this->type == self::TYPE_CHECK_OUT;
    }

    function wasToday(){
        return $this->time->isToday();
    }

    function isByStaff(){ return $this->by_type == self::BY_STAFF; }

    function isByVisitor(){ return $this->by_type == self::BY_VISITOR; }

    function isByCompanyVehicle(){ return $this->by_type == self::BY_COMPANY_VEHICLE; }
}
