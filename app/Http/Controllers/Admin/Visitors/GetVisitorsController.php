<?php

namespace App\Http\Controllers\Admin\Visitors;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetVisitorsController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');

        $q = Visitor::query()->with('any_last_visit', 'any_last_visit.site');
        if($order == 'recent') $q->latest();
        elseif($order == 'az') $q->orderBy('name', 'ASC');
        elseif($order == 'za') $q->orderBy('name', 'DESC');

        return response()->view('admin.visitors.all',[
            'result' => new ResultSet($q, $limit, function($user){
                // $c = $user->created_at;
                // $user->time = substr($c->monthName, 0, 3).' '.$c->day;

                // if(!$c->isCurrentYear()){
                //     $user->time .= ', '.$c->year;
                // }

                // return $user;
            })
        ]);
    }
}
