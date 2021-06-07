<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetVehiclesController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');

        $q = Vehicle::query();
        if($order == 'az') $q->orderBy('registration_no', 'ASC');
        elseif($order == 'za') $q->orderBy('registration_no', 'DESC');
        elseif($order == 'desc') $q->orderBy('description', 'ASC');

        return response()->view('admin.vehicles.all',[
            'result' => new ResultSet($q, $limit)
        ]);
    }
}
