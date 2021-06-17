<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Exports\CompanyVehiclesActivityExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Drive;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehicleActivityController extends Controller
{
    function company(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = Drive::with('vehicle', 'driveable_in', 'driveable_out');

        $order = $request->get('order');

        if($order == 'past') $q->oldest('time_in');
        else $q->latest('time_in');


        if($request->filled('keyword')){
            $q->where(function($q1) use($request){
                $q1->whereHas('vehicle', function($q2) use($request){
                    $k = '%'.$request->get('keyword').'%';
                    $q2->where('registration_no', 'like', $k)
                        ->orWhere('description', 'like', $k);
                })
                ->orWhereHas('driveable_in', function($q2) use($request){
                    $k = '%'.$request->get('keyword').'%';
                    $q2->where('name', 'like', $k);
                });
            });
        }


        $dates = null;

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

        return response()->view('admin.vehicles.company_stats',[
            'result' => new ResultSet($q, $limit),
            'dates' => $dates
        ]);
    }

    function exportCompany(){
        return new CompanyVehiclesActivityExport();
    }
}
