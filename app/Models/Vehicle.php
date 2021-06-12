<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'registration_no',
        'description',
        'vehicleable_id',
        'vehicleable_type',
        'slug',
    ];

    function vehicleable(){
        return $this->morphTo();
    }
}
