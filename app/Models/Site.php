<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
    ];

    public $timestamps = false;

    function companies(){
        return $this->hasMany(Company::class);
    }

    function users(){
        return $this->hasMany(User::class);
    }
}
