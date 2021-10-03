<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id_number',
        'id_photo',
        'phone',
        'from'
    ];

    public $timestamps = false;

    function activities(){ return $this->morphMany(Activity::class, 'by'); }

    function last_activity(){
        return $this->hasOne(Activity::class, 'by_id')
            ->byVisitor()
            ->latest('time');
    }

    function check_ins(){ return $this->activities()->checkIn(); }

    function check_outs(){ return $this->activities()->checkOut(); }

    function vehicles(){ return $this->morphMany(Vehicle::class, 'owner'); }


    function getFirstNameAttribute(){
        return preg_split('/ +/', $this->name)[0];
    }
}
