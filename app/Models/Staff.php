<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    protected $with = ['vehicles', 'company'];

    function company(){ return $this->belongsTo(Company::class); }

    function visits(){ return $this->hasMany(Visit::class); }

    function activities(){ return $this->morphMany(Activity::class, 'by'); }

    function last_activity(){
        return $this->hasOne(Activity::class, 'by_id')
            ->byStaff()
            ->latest('time');
    }

    function last_check_in(){
        return $this->last_activity()->checkIn();
    }

    function last_check_out(){
        return $this->last_activity()->checkOut();
    }

    function check_ins(){ return $this->activities()->checkIn(); }

    function check_outs(){ return $this->activities()->checkOut(); }

    function vehicles(){ return $this->morphMany(Vehicle::class, 'owner'); }

    function getFirstNameAttribute(){
        return preg_split('/ +/', $this->name)[0];
    }



    protected static function booted()
    {
        parent::booted();
        // static::addGlobalScope('ancient', function (Builder $builder) {
        //     $builder->whereHas('company');
        // });
    }
}
