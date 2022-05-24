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

    protected $keyType = 'string';

    function __construct($data = []){
        parent::__construct($data);

        $this->created_at = $data['timestamp'] ?? null;
        if(isset($data['staff'])) $this->staff = new Staff($data['staff'] ?? []);
        if(isset($data['company'])) $this->company = new Company($data['company']);
        if(isset($data['activity'])) $this->activity = new Activity($data['activity'] ?? []);

        $this->staff_id = $data['staff']['id'] ?? null;
        $this->company_id = $data['company']['id'] ?? null;
        $this->activity_id = $data['activity']['id'] ?? null;
    }

    function staff(){ return $this->belongsTo(Staff::class); }

    function company(){ return $this->belongsTo(Company::class); }

    function activity(){ return $this->belongsTo(Activity::class); }


    function scopeNoCardIssued($q){ $q->where('card_number', null); }


    function getFmtHostAttribute(){
        if($this->staff){
            return $this->staff->name.($this->company ? (' - '.$this->company->name) : '');
        }

        return ($this->host != null ? $this->host : 'None').($this->company ? (' - '.$this->company->name) : '');
    }
}
