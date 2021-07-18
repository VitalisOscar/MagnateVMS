<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitEnquiryController extends Controller
{

    function getVisitor(Request $request){
        $visitor = Visitor::where('id_number', $request->get('id_number'))->first();

	    // get staff members
	    $staff = Staff::whereHas('company', function($q){
		    $q->where('site_id', auth('sanctum')->user()->site_id);
	    })
	    ->with('company')
	    ->get()
	    ->each(function($s){
		    $s->company_name = $s->company->name;
	    });

        if($visitor != null){
            return $this->json->data(['visitor' => $visitor, 'staff' => $staff]);
        }

        return $this->json->data(['staff' => $staff], 'Visitor record does not exist');
    }

    function getVisitForCheckOut(Request $request){
        $visitor = Visitor::where('id_number', $request->get('id_number'))->first();

        if($visitor == null){
            return $this->json->data(null, 'Visit record does not exist');
        }

        $last_visit = $visitor->last_visit;

	    if(!$last_visit->wasToday()){
            return $this->json->data(null, 'Visit record does not exist');
        }

        $last_visit->load('staff');
        $last_visit->staff->load('company');

        $last_visit->staff_name = $last_visit->staff->name;
        $last_visit->company = $last_visit->staff->company->name;

        $m = "Visitor was checked in <time> by ".$last_visit->check_in_user->username;
        $in = Carbon::createFromTimeString($last_visit->time_in);
        if($in->isToday()) $m = str_replace("<time>", 'at '.$in->format('H:i'), $m);
        elseif($in->isYesterday()) $m = str_replace("<time>", 'yesterday at '.$in->format('H:i'), $m);
        elseif($in->isCurrentYear()) $m = str_replace("<time>", (substr($in->monthName, 0, 3).' '.$in->day).' at '.$in->format('H:i'), $m);
        else $m = str_replace("<time>", (substr($in->monthName, 0, 3).' '.$in->day.', '.$in->year).' at '.$in->format('H:i'), $m);

        $last_visit->check_in_time = $m;

        return $this->json->data([
            'visitor' => $visitor,
            'visit' => $last_visit,
        ]);
    }
}
