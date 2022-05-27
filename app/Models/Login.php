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

    public $timestamps = false;

    public $fillable = [
        'id',
        'user_id',
        'site_id',
        'user_type',
        'user_agent',
        'ip_address',
        'timestamp'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected $keyType = 'string';

    function __construct($data = []){
        parent::__construct($data);

        $this->created_at = $data['timestamp'] ?? null;
        if(isset($data['user'])) $this->user = new User($data['user'] ?? []);
        if(isset($data['site'])) $this->site = new Site($data['site'] ?? []);

        $this->site_id = $data['site']['id'] ?? null;
        $this->user_id = $data['user']['id'] ?? null;
    }

    function getFmtDateAttribute(){
        return $this->created_at->day.' '.substr($this->created_at->monthName, 0, 3).' '.$this->created_at->year;
    }

    function getFmtTimeAttribute(){
        if(is_string($this->created_at)){
            $d = Carbon::createFromTimeString($this->created_at);
        }else if($this->created_at == null){
            $d = Carbon::now();
        }else{
            $d = $this->created_at;
        }

        return $d->format('H:i');
    }

    function getFmtDatetimeAttribute(){
        return $this->fmt_date.' at '.$this->fmt_time;
    }

    function user(){ return $this->belongsTo(User::class); }
    function site(){ return $this->belongsTo(Site::class); }
}
