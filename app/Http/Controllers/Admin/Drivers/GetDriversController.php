<?php

namespace App\Http\Controllers\Admin\Drivers;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class GetDriversController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');

        $q = Driver::query();
        if($order == 'az') $q->orderBy('name', 'ASC');
        elseif($order == 'za') $q->orderBy('name', 'DESC');

        return response()->view('admin.drivers.all',[
            'result' => new ResultSet($q, $limit)
        ]);
    }
}
