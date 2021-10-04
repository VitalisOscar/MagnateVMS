<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'reason',
        'company_id',
        'staff_id',
        'host',
	    'items',
        'from', // company from
        'card_number',
        'signature',
        'check_in_visit_id'
    ];

    public $timestamps = false;

    protected $appends = ['fmt_host'];

    function staff(){ return $this->belongsTo(Staff::class); }

    function company(){ return $this->belongsTo(Company::class); }

    function activity(){ return $this->belongsTo(Activity::class); }

    function getFmtHostAttribute(){
        if($this->staff){
            return $this->staff->name.' - '.$this->company->name;
        }

        return ($this->host != null ? $this->host : 'None').' - '.$this->company->name;
    }
}
