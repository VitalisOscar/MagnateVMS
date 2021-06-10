<?php

namespace App\Http\Controllers\Admin\Visitors;

use App\Exports\AllVisitsExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VisitsController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = Visit::query()
            ->with([
                'visitor',
                'site',
                'staff',
                'staff.company',
                'check_in_user',
                'check_out_user'
            ]);

        $order = $request->get('order');

        if($order == 'past') $q->oldest('time_in');
        else $q->latest('time_in');


        $dates = null;

        if($request->filled('site')){
            $q->whereSiteId($request->get('site'));
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

        $visits = new ResultSet($q, $limit);

        return response()->view('admin.visitors.visits', [
            'result' => $visits,
            'dates' => $dates
        ]);
    }

    function export(){
        return new AllVisitsExport();
    }
}
