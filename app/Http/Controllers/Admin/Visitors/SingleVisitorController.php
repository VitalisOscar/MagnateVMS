<?php

namespace App\Http\Controllers\Admin\Visitors;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $order = $request->get('order');

        $q = Visitor::query()->with('any_last_visit', 'any_last_visit.site');
        if($order == 'recent') $q->latest();
        elseif($order == 'az') $q->orderBy('name', 'ASC');
        elseif($order == 'za') $q->orderBy('name', 'DESC');

        $q = $visitor->visits()
            ->latest('time_in')
            ->with([
                'site',
                'staff',
                'staff.company',
                'check_in_user',
                'check_out_user'
            ]);

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

        return response()->view('admin.visitors.single', [
            'visitor' => $visitor,
            'result' => $visits,
            'dates' => $dates
        ]);
    }
}
