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

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected $with = ['last_login'];

    protected $hidden = ['password'];

    function site(){
        return $this->belongsTo(Site::class, 'site_id');
    }

    function check_ins(){
        return $this->hasMany(Visit::class, 'checked_in_by');
    }

    function check_outs(){
        return $this->hasMany(Visit::class, 'checked_out_by');
    }

    function logins(){
        return $this->hasMany(Login::class, 'identifier');
    }

    function last_login(){
        return $this->hasOne(Login::class, 'identifier')->latest('time')->successful();
    }

    function getSiteIdAttribute(){
        return $this->last_login->site_id;
    }
}
