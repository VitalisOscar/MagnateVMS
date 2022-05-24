<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverActivity extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'id',
        'driver_id',
        'activity_id',
        'mileage',
        'task',
        'comments'
    ];

    protected $appends = ['fmt_mileage'];

    protected $keyType = 'string';

    function __construct($data = []){
        parent::__construct($data);

        $this->created_at = $data['timestamp'] ?? null;

        if(isset($data['driver'])) $this->driver = new Driver($data['driver'] ?? []);
        if(isset($data['vehicle'])) $this->vehicle = new Vehicle($data['vehicle'] ?? []);
        if(isset($data['activity'])) $this->activity = new Activity($data['activity'] ?? []);

        $this->driver_id = $data['driver']['id'] ?? null;
        $this->vehicle_id = $data['vehicle']['id'] ?? null;
        $this->activity_id = $data['activity']['id'] ?? null;
    }

    function driver(){ return $this->belongsTo(Driver::class); }

    function activity(){ return $this->belongsTo(Activity::class); }

    function getFmtMileageAttribute(){
        return number_format($this->mileage);
    }
}
