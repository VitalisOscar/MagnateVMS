<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    const TRACKABLES = [
        'visitors' => 'Visitors and their vehicles',
        'access_cards' => 'Visitor access cards',
        'staff' => 'Staff and their vehicles',
        'parked_vehicles' => 'Parked vehicles',
        'company_vehicles' => 'Company vehicles',
    ];

    public $fillable = [
        'name', 'options'
    ];

    public $casts = [
        'options' => 'array',
    ];

    public $timestamps = false;

    function companies(){
        return $this->hasMany(Company::class);
    }

    function logins(){
        return $this->hasMany(Login::class);
    }

    function staff(){
        return $this->hasManyThrough(Staff::class, Company::class);
    }

    function loginsAreEnabled(){
        return isset($this->options['logins']) && $this->options['logins'];
    }

    function tracks($trackable){
        return isset($this->options['tracking'][$trackable]) && $this->options['tracking'][$trackable];
    }
}
