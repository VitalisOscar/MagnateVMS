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
    ];

    // primary key type to string
    protected $keyType = 'string';

    // public $withCount = ['staff'];

    public $timestamps = false;

    function staff(){ return $this->hasMany(Staff::class); }

    function site(){ return $this->belongsTo(Site::class); }
}
