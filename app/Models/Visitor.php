<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'id_number',
        'id_photo',
        'phone',
        'from'
    ];

    // primary key type to string
    protected $keyType = 'string';

    public $timestamps = false;

    function __construct($data = []){
        parent::__construct($data);

        $this->last_activity = new Activity($data['last_activity'] ?? []);
    }

    function activities(){ return $this->morphMany(Activity::class, 'by'); }

    function first_activity(){
        return $this->hasOne(Activity::class, 'by_id')
            ->byVisitor()
            ->with('visit')
            ->orderBy('time', 'asc');
    }

    function last_activity(){
        return $this->hasOne(Activity::class, 'by_id')
            ->byVisitor()
            ->with('visit')
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
}
