<?php

namespace App\Http\Controllers\Data\Visitors;

use App\Exports\SingleVisitorExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class SingleVisitorController extends Controller
{
    function get(Request $request, $visitor_id){
        $visitor = Visitor::whereId($visitor_id)
            ->first();

        if($visitor == null){
            return redirect()->route('admin.visitors');
        }


        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = $visitor->activities()
            ->with('site', 'vehicle', 'user', 'visit', 'visit.staff', 'visit.company');

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

        return response()->view('admin.visitors.single', [
            'visitor' => $visitor,
            'result' => $visits,
            'dates' => $dates
        ]);
    }

    function getByIdNumber(Request $request){
        return $this->json->data(Visitor::where('id_number', $request->get('id_number'))->first());
    }

    function export($visitor_id){
        return new SingleVisitorExport($visitor_id);
    }
}
