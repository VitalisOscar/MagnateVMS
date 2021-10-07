<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = "staff";

    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'extension',
        'department',
    ];

    protected $casts = [
        'added_at' => 'datetime'
    ];

    protected $with = ['vehicles'];

    function company(){ return $this->belongsTo(Company::class); }

    function visits(){ return $this->hasMany(Visit::class); }

    function activities(){ return $this->morphMany(Activity::class, 'by'); }

    function last_activity(){
        return $this->hasOne(Activity::class, 'by_id')
            ->byStaff()
            ->latest('time');
    }

    function check_ins(){ return $this->activities()->checkIn(); }

    function check_outs(){ return $this->activities()->checkOut(); }

    function vehicles(){ return $this->morphMany(Vehicle::class, 'owner'); }

    function getFirstNameAttribute(){
        return preg_split('/ +/', $this->name)[0];
    }
}
