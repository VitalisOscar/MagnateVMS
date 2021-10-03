<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    use HasFactory;

    const TYPE_USER = 'User';
    const TYPE_ADMIN = 'Admin';

    const STATUS_SUCCESS = 'Success';
    const STATUS_FAILED = 'Failed';
    const STATUS_INVALID_CREDENTIAL = 'Invalid Credential';
    const STATUS_INVALID_PASSWORD = 'Invalid Password';

    public $timestamps = false;

    public $fillable = [
        'credential',
        'user_id',
        'user_type',
        'user_agent',
        'site_id',
        'ip_address',
        'status',
    ];

    protected $casts = [
        'time' => 'datetime'
    ];

    function site(){
        return $this->belongsTo(Site::class);
    }

    function user(){
        return $this->morphTo();
    }

    function scopeSuccessful($q){
        $q->whereStatus(self::STATUS_SUCCESS);
    }

    function scopeByUser($q){
        $q->whereUserType(self::TYPE_USER);
    }

    function scopeByAdmin($q){
        $q->whereUserType(self::TYPE_ADMIN);
    }
}
