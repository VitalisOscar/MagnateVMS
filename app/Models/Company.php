<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'site_id',
        'name',
        'total_staff'
    ];

    protected $with = ['site'];

    // primary key type to string
    protected $keyType = 'string';

    // public $withCount = ['staff'];

    public $timestamps = false;

    function getTotalStaffAttribute($val) {
        if($val == null) return 0;
        return $val;
    }

    function staff(){ return $this->hasMany(Staff::class); }

    function site(){ return $this->belongsTo(Site::class); }
}
