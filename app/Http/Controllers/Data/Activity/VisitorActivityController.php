<?php

namespace App\Http\Controllers\Data\Activity;

use App\Exports\AllVisitsExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class VisitorActivityController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = Activity::byVisitor()
            ->with('by', 'site', 'vehicle', 'user', 'visit', 'visit.staff', 'visit.company');

        $order = $request->get('order');

        if($order == 'past') $q->oldest('time');
        else $q->latest('time');


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

            $q->whereDate('time', '>=', $from)
                ->whereDate('time', '<=', $to);

            $dates = $from.' to '.$to;
        }

        $visits = new ResultSet($q, $limit);

        return $request->is('api*') ?
            $this->json->mixed($visits, $visits->items) :
            response()->view('admin.activity.visitors', [
                'result' => $visits,
                'dates' => $dates
            ]);
    }

    function export(){
        return new AllVisitsExport();
    }
}
