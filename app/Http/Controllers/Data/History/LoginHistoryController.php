<?php

namespace App\Http\Controllers\Data\History;

use App\Exports\LoginsExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Login;
use Exception;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');

        $q = Login::where('user_id', '<>', null)->with('user', 'site');
        if($order == 'oldest') $q->oldest('time');
        else $q->latest('time');

        $dates = null;

        if($request->filled('site')){
            $q->whereSiteId($request->get('site'));
        }

        if($request->filled('type')){
            $q->whereUserType($request->get('type'));
        }

        if($request->filled('date')){
            $date = explode(' to ', $request->get('date'));
            if(count($date) == 1){
                $from = $date[0];
                $to = $date[0];
            }else{
                $from = $date[0];
                $to = $date[1];

                if($from > $to){
                    $x = $from;
                    $from = $to;
                    $to = $x;
                }
            }

            $q->whereDate('time', '>=', $from)
                ->whereDate('time', '<=', $to);

            $dates = $from.' to '.$to;
        }

        if($request->filled('keyword')){
            $keyword = "%".$request->get('keyword')."%";
            $q->whereHas('user', function($q2) use ($keyword){
                $q2->where('name', 'like', $keyword);
            });
        }


        return response()->view('admin.history.logins',[
            'result' => new ResultSet($q, $limit),
            'dates' => $dates
        ]);
    }

    function export(){
        return new LoginsExport();
    }
}
