<?php

namespace App\Http\Controllers\Data\Visitors;

use App\Exports\AllVisitorsExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class GetVisitorsController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');

        $q = Visitor::query()->with('last_activity', 'last_activity.site');

        if($request->filled('keyword')){
            $keyword = "%".$request->get('keyword')."%";
            $q->where(function($q1) use($keyword){
                $q1->where('name', 'like', $keyword)
                    ->orWhere('id_number', 'like', $keyword)
                    ->orWhere('phone', 'like', $keyword)
                    ->orWhere('from', 'like', $keyword);
            });
        }

        if($order == 'recent') $q->orderBy('id', 'desc');
        elseif($order == 'az') $q->orderBy('name', 'ASC');
        elseif($order == 'za') $q->orderBy('name', 'DESC');

        return response()->view('admin.visitors.all',[
            'result' => new ResultSet($q, $limit)
        ]);
    }

    function export(){
        return new AllVisitorsExport();
    }
}
