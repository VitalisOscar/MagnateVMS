<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    use HasFactory;

    const TYPE_USER = 'user';
    const TYPE_ADMIN = 'admin';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_INVALID_CREDENTIAL = 'invalid_credential';
    const STATUS_INVALID_PASSWORD = 'invalid_password';

    public $timestamps = false;

    public $fillable = [
        'type',
        'credential',
        'identifier',
        'user_agent',
        'site_id',
        'ip_address',
        'status',
    ];

    function site(){
        return $this->belongsTo(Site::class);
    }

    function user(){
        return $this->belongsTo(User::class, 'identifier')->whereType(self::TYPE_USER);
    }

    function admin(){
        return $this->belongsTo(Admin::class, 'identifier')->whereType(self::TYPE_ADMIN);
    }

    function scopeSuccessful($q){
        $q->whereStatus(self::STATUS_SUCCESS);
    }

    function scopeByUser($q){
        $q->whereType(self::TYPE_USER);
    }

    function scopeByAdmin($q){
        $q->whereType(self::TYPE_ADMIN);
    }

    function getTimeAttribute($val){
        /** @var Carbon */
        $t = Carbon::createFromTimeString($val);
        $now = Carbon::now();

        if($t->isToday()){
            return 'Today ' .$t->format('H:i');
        }else if($t->isYesterday()){
            return 'Yesterday at ' .$t->format('H:i');
        }

        if($t->isCurrentYear())
        return substr($t->monthName, 0, 3).' '.$t->day.' at '.$t->format('H:i');

        return substr($t->monthName, 0, 3).' '.$t->day.', '.$t->year.' at '.$t->format('H:i');
    }
}
