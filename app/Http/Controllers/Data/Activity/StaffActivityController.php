<?php

namespace App\Http\Controllers\Data\Activity;

use App\Exports\AllVisitsExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Company;
use Illuminate\Http\Request;

class StaffActivityController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = Activity::byStaff()
            ->with('by', 'by.company', 'site', 'vehicle', 'user');

        $order = $request->get('order');

        if($order == 'past') $q->oldest('time');
        else $q->latest('time');


        $dates = null;

        if($request->filled('keyword')){
            $k = '%'.$request->get('keyword').'%';
            $q->whereHas('staff', function($q2) use($k){
                $q2->where('name', 'like', $k);
            });
        }

        if($request->filled('company')){
            $q->whereHas('staff', function($s) use($request){
                $s->whereHas('company', function($c) use($request){
                    $c->whereId($request->get('company'));
                });
            });
        }

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

        $activities = new ResultSet($q, $limit);

        return $request->is('api*') ?
            $this->json->mixed($activities, $activities->items) :
            response()->view('admin.activity.staff', [
                'result' => $activities,
                'dates' => $dates,
                'companies' => Company::whereHas('site')->with('site')->get()
            ]);
    }

    function export(){
        return new AllVisitsExport();
    }
}
