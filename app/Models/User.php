<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected $with = ['last_login'];

    protected $hidden = ['password'];

    protected $appends = ['date_added'];

    function site(){ return $this->belongsTo(Site::class, 'site_id'); }

    function activities(){ return $this->hasMany(Activity::class); }

    function check_ins(){ return $this->activities()->checkIn(); }

    function check_outs(){ return $this->activities()->checkOut(); }

    function logins(){ return $this->morphMany(Login::class, 'user'); }

    function last_login(){
        return $this->morphOne(Login::class, 'user')->latest('time')->successful();
    }

    function getSiteIdAttribute(){
        return $this->last_login ? $this->last_login->site_id:null;
    }

    function getDateAddedAttribute(){
        return substr($this->created_at->monthName, 0, 3).' '.$this->created_at->day.' '.$this->created_at->year;
    }
}
