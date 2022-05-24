<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    const OWNER_STAFF = 'Staff';
    const OWNER_VISITOR = 'Visitor';
    const OWNER_COMPANY = null;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'registration_no',
        'description',
        'owner_id',
        'owner_type',
    ];

    // primary key type to string
    protected $keyType = 'string';

    function __construct($data = []){
        parent::__construct($data);

        $this->created_at = $data['timestamp'] ?? null;

        if(isset($data['owner'])){
            if($this->isStaffVehicle()){
                $this->owner = new Staff($data['owner'] ?? []);
            }

            if($this->isVisitorVehicle()){
                $this->owner = new Staff($data['owner'] ?? []);
            }
        }

        if(isset($data['last_activity'])) $this->last_activity = new Activity($data['last_activity'] ?? []);

        $this->owner_id = $data['owner']['id'] ?? null;
    }

    function owner(){ return $this->morphTo('owner'); }

    function activities(){ return $this->morphMany(Activity::class, 'by'); }

    function usages(){ return $this->hasMany(Activity::class, 'vehicle_id'); }

    function check_ins(){ return $this->activities()->checkIn(); }

    function check_outs(){ return $this->activities()->checkOut(); }

    function last_activity(){
        return $this->hasOne(Activity::class, 'by_id')
            ->byCompanyVehicle()
            ->latest('time');
    }

    function last_check_in(){
        return $this->last_activity()->checkIn();
    }

    function last_check_out(){
        return $this->last_activity()->checkOut();
    }

    function drives(){
        // return $this->hasMany(Drive::class);
    }

    function last_drive(){
        // return $this->hasOne(Drive::class)->latest('time_out');
    }

    function scopeCompanyOwned($q){
        $q->where('owner_type', self::OWNER_COMPANY);
    }

    function scopeStaffOwned($q){
        $q->where('owner_type', self::OWNER_STAFF)->whereHas('owner');
    }

    function scopeVisitorOwned($q){
        $q->where('owner_type', self::OWNER_VISITOR)->whereHas('owner');
    }

    function scopeOtherOwned($q){
        $q->where('owner_type', '<>', self::OWNER_COMPANY)->whereHas('owner');
    }

    function scopeRegNo($q, $registration_no){
        $registration_no = strtoupper(preg_replace("/ +/", " ", $registration_no));

        $q->where('registration_no', $registration_no);
    }


    // Accessors
    function getOwnerNameAttribute(){
        if($this->isCompanyVehicle()) return 'Company';

        return $this->owner->name;
    }


    function isCompanyVehicle(){
        return $this->owner_type == self::OWNER_COMPANY;
    }

    function isStaffVehicle(){
        return $this->owner_type == self::OWNER_STAFF;
    }

    function isVisitorVehicle(){
        return $this->owner_type == self::OWNER_VISITOR;
    }
}
