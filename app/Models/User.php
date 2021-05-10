<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasApiTokens;

    protected $fillable = [
        'name',
        'username',
        'site_id',
        'password',
    ];

    protected $hidden = ['password', 'created_at', 'updated_at', 'site_id'];

    function site(){
        return $this->belongsTo(Site::class);
    }

    function check_ins(){
        return $this->hasMany(Visit::class, 'checked_in_by');
    }

    function check_outs(){
        return $this->hasMany(Visit::class, 'checked_out_by');
    }
}
