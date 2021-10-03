<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;

class Admin extends User
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'name', 'username', 'password'
    ];
}
