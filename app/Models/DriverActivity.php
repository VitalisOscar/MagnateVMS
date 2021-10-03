<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverActivity extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'driver_id',
        'activity_id',
        'mileage',
        'task'
    ];

    function driver(){ return $this->belongsTo(Driver::class); }

    function activity(){ return $this->belongsTo(Activity::class); }

    function getFmtMileageAttribute(){
        return number_format($this->mileage);
    }
}
