<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Exports\VehiclesExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetVehiclesController extends Controller
{
    function company(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');

        $q = Vehicle::companyOwned();

        if($request->filled('keyword')){
            $k = '%'.$request->get('keyword').'%';

            $q->where(function($q1) use($k){
                $q1->where('registration_no', 'like', $k)
                    ->orWhere('description', 'like', $k);
            });
        }

        if($order == 'az') $q->orderBy('registration_no', 'ASC');
        elseif($order == 'za') $q->orderBy('registration_no', 'DESC');

        return response()->view('admin.vehicles.company',[
            'result' => new ResultSet($q, $limit)
        ]);
    }

    function other(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');
        $show = $request->get('show');

        if($show == 'staff') $q = Vehicle::staffOwned();
        elseif($show == 'visitors') $q = Vehicle::visitorOwned();
        else $q = Vehicle::otherOwned();

        if($request->filled('keyword')){
            $k = '%'.$request->get('keyword').'%';

            $q->where(function($q1) use($k){
                $q1->where('registration_no', 'like', $k)
                    ->orWhere('description', 'like', $k)
                    ->orWhereHas('vehicleable', function($q2) use($k){
                        $q2->where('name', 'like', $k);
                    });
            });
        }

        if($order == 'az') $q->orderBy('registration_no', 'ASC');
        elseif($order == 'za') $q->orderBy('registration_no', 'DESC');

        return response()->view('admin.vehicles.others',[
            'result' => new ResultSet($q, $limit)
        ]);
    }

    function exportOther(){
        return new VehiclesExport("other");
    }

    function exportCompany(){
        return new VehiclesExport('company');
    }
}
