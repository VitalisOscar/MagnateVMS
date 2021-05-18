<?php

namespace App\Http\Controllers\Api\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitorEnquiryController extends Controller
{
    function getCheckedIn(Request $request){
        return $this->json->mixed(null,
	    Visitor::whereHas('last_visit', function($q){
		$q->stillIn()->atSite()->today();
    	})
	//->orderByRaw('last_visit.time_in ASC')
	->with('last_visit', 'last_visit.staff', 'last_visit.staff.company', 'last_visit.check_in_user')
        ->get()
        ->each(function ($visitor){
            $visit = $visitor->last_visit;

            $visit->staff_name = $visit->staff->name;
            $visit->company = $visit->staff->company->name;

            $visitor->reason = $visit->reason;
            $visitor->from = $visit->from;
            $visitor->visitor_id = $visitor->id;

            $in = Carbon::createFromTimeString($visit->time_in);

            $m = "Visitor was checked in <time> by ".$visit->check_in_user->username;
            if($in->isToday()){
                $time_in = $in->format('H:i');
                $m = str_replace("<time>", 'at '.$time_in, $m);
            }elseif($in->isYesterday()){
                $time_in = $in->format('H:i');
                $m = str_replace("<time>", 'yesterday at '.$time_in, $m);
            }elseif($in->isCurrentYear()){
                $time_in = substr($in->monthName, 0, 3).' '.$in->day.' '.$in->format('H:i');
                $m = str_replace("<time>", (substr($in->monthName, 0, 3).' '.$in->day).' at '.$in->format('H:i'), $m);
            }else{
                $time_in = substr($in->monthName, 0, 3).' '.$in->day.', '.$in->year.' '.$in->format('H:i');
                $m = str_replace("<time>", (substr($in->monthName, 0, 3).' '.$in->day.', '.$in->year).' at '.$in->format('H:i'), $m);
            }

            $visit->check_in_time = $m;

            $visit->time_in = $time_in;
            $visit->time_out = $visit->time_out == null ? null:Carbon::createFromTimeString($visit->time_out)->format('H:i');

	        $visitor->visit = $visit;
        })
        );

    }
}
