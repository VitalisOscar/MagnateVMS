<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Exports\AllStaffCheckInsExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\StaffCheckIn;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffCheckInController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = StaffCheckIn::query()
            ->with('staff', 'staff.company', 'site');

        $order = $request->get('order');

        if($order == 'past') $q->oldest('time_in');
        else $q->latest('time_in');


        $dates = null;

        if($request->filled('site')){
            $q->where('site_id', $request->get('site'));
        }

        if($request->filled('company')){
            $q->whereHas('staff', function($q1) use($request){
                $q1->whereCompanyId($request->get('company'));
            });
        }

        if($request->filled('date')){
            $date = explode(' to ', $request->get('date'));
            if(count($date) == 1){
                $from = $date[0];
                $to = $date[0];
            }else{
                $from = $date[0];
                $to = $date[1];
            }

            if($from > $to){
                $x = $from;
                $from = $to;
                $to = $x;
            }

            $q->whereDate('time_in', '>=', $from)
                ->whereDate('time_in', '<=', $to);

            $dates = $from.' to '.$to;
        }

        return response()->view('admin.staff.checkins',[
            'result' => new ResultSet($q, $limit),
            'dates' => $dates
        ]);
    }

    function export(){
        return new AllStaffCheckInsExport();
    }
}
