<?php

namespace App\Http\Controllers\Data\Activity;

use App\Exports\AllVisitsExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Visitor;
use Carbon\Carbon;
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

        if($request->is('api*')){
            $q->atSite(auth('sanctum')->user()->site_id);
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
        }else if($request->is('api*')){
            $d = Carbon::today()->format('Y-m-d');

            $q->whereDate('time', '>=', $d)
                ->whereDate('time', '<=', $d);
        }

        $visits = new ResultSet($q, $limit);

        return $request->is('api*') ?
            $this->json->mixed($visits, $visits->items) :
            response()->view('admin.activity.visitors', [
                'result' => $visits,
                'dates' => $dates
            ]);
    }

    function getCheckedIn(){
        $query = Visitor::whereHas('last_check_in', function($a){
                $a->onDate(Carbon::today())
                    ->atSite(auth('sanctum')->user()->site_id)
                    ->notCheckedOut();
            })
            ->with([
                'last_activity', 'last_activity.visit',
                'last_activity.vehicle', 'last_activity.visit.company', 'last_activity.visit.staff'
            ]);

        $result = new ResultSet($query);

        return $this->json->mixed(null, $result->items);
    }

    function export(){
        return new AllVisitsExport();
    }
}
