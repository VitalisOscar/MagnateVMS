<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public $fillable = [
        'site_id',
        'name',
    ];

    public $withCount = ['staff'];

    public $timestamps = false;

    function staff(){ return $this->hasMany(Staff::class); }

    function site(){ return $this->belongsTo(Site::class); }
}
