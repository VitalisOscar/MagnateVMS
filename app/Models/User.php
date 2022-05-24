<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected $hidden = ['password'];

    protected $appends = ['date_added'];

    protected $keyType = 'string';

    function __construct($data = []){
        parent::__construct($data);

        $this->created_at = $data['timestamp'] ?? null;
        if(isset($data['last_login'])) $this->last_login = new Login($data['last_login'] ?? []);
    }

    function getDateAddedAttribute(){
        return substr($this->created_at->monthName, 0, 3).' '.$this->created_at->day.' '.$this->created_at->year;
    }
}
