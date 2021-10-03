<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    const OWNER_STAFF = 'Staff';
    const OWNER_VISITOR = 'Visitor';
    const OWNER_COMPANY = 'Company';

    public $timestamps = false;

    protected $fillable = [
        'registration_no',
        'description',
        'owner_id',
        'owner_type',
    ];

    function owner(){ return $this->morphTo('owner'); }

    function activities(){ return $this->morphMany(Activity::class, 'by'); }

    function check_ins(){ return $this->activities()->checkIn(); }

    function check_outs(){ return $this->activities()->checkOut(); }

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
        $q->where('owner_type', self::OWNER_STAFF);
    }

    function scopeVisitorOwned($q){
        $q->where('owner_type', self::OWNER_VISITOR);
    }

    function scopeOtherOwned($q){
        $q->where('owner_type', '<>', self::OWNER_COMPANY);
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
