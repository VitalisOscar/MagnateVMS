<?php

namespace App\Models;

use Carbon\Carbon;
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


    function getFmtDateAttribute(){
        return $this->time->day.' '.substr($this->time->monthName, 0, 3).' '.$this->time->year;
    }

    function getFmtTimeAttribute(){
        if(is_string($this->time)){
            $d = Carbon::createFromTimeString($this->time);
        }else if($this->time == null){
            $d = Carbon::now();
        }else{
            $d = $this->time;
        }

        return $d->format('H:i');
    }

    function getFmtDatetimeAttribute(){
        return $this->fmt_date.' at '.$this->fmt_time;
    }
}
