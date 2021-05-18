<?php

namespace App\Http\Controllers\Api\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitorRecordsController extends Controller
{
    function __invoke(Request $request){
        $date = $request->filled('date') ?
            Carbon::createFromFormat('Y-m-d', $request->get('date')):
            Carbon::today()->format('Y-m-d');

        $q = Visit::whereDate('time_in', $date);

        $total = $q->count();

        $q->with('visitor', 'visitor.visits');

        $res = $q->get()->each(function($visit){
            $visitor = $visit->visitor;

            $visit->name = $visitor->name;
            $visit->staff_name = $visit->staff->name;
            $visit->company = $visit->staff->company->name;

            $in = Carbon::createFromTimeString($visit->time_in);

            $m = "Visitor was checked in <time> by ".$visit->check_in_user->username;
            $in = Carbon::createFromTimeString($visit->time_in);
            if($in->isToday()) $m = str_replace("<time>", 'at '.$in->format('H:i'), $m);
            elseif($in->isYesterday()) $m = str_replace("<time>", 'yesterday at '.$in->format('H:i'), $m);
            elseif($in->isCurrentYear()) $m = str_replace("<time>", (substr($in->monthName, 0, 3).' '.$in->day).' at '.$in->format('H:i'), $m);
            else $m = str_replace("<time>", (substr($in->monthName, 0, 3).' '.$in->day.', '.$in->year).' at '.$in->format('H:i'), $m);

            $visit->check_in_time = $m;

            $visit->time_in = '11:23';
        });

        return $this->json->mixed(['total' => $total], $res);
    }
}
