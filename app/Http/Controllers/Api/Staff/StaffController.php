<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffCheckIn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    function getCheckedIn(Request $request){
        return $this->json->mixed(null,
            StaffCheckIn::latest('time_in')
                ->stillIn()
                ->atSite()
                ->with('staff', 'staff.company')
                ->get()
                ->each(function ($checkin){
                    $checkin->company_name = $checkin->staff->company->name;
                    $checkin->name = $checkin->staff->name;

                    $checkin->car_registration = $checkin->car_registration == null ? 'No Car':$checkin->car_registration;

                    $in = Carbon::createFromTimeString($checkin->time_in);

                    if($in->isToday()) $m = $in->format('H:i');
                    elseif($in->isYesterday()) $m = 'Yesterday '.$in->format('H:i');
                    elseif($in->isCurrentYear()) $m = (substr($in->monthName, 0, 3).' '.$in->day).' '.$in->format('H:i');
                    else $m = (substr($in->monthName, 0, 3).' '.$in->day.', '.$in->year).' '.$in->format('H:i');

                    $checkin->time_in = $m;
                })
        );
    }

    function getAll(){
        return $this->json->items(Staff::all());
    }
}
