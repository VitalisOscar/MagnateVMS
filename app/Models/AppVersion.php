<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;

    protected $table = 'app_versions';

    public $timestamps = false;

    protected $fillable = [
        'version',
        'url',
    ];

    protected $casts = [
        'time' => 'datetime'
    ];

    protected $appends = [
        'fmt_url', 'date'
    ];

    function getFmtUrlAttribute(){
        return asset('storage/'.$this->url);
    }

    function getDateAttribute(){
        return $this->time->day.' '.substr($this->time->monthName, 0, 3).' '.$this->time->year;
    }
}
