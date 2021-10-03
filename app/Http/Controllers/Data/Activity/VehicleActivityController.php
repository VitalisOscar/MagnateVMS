<?php

namespace App\Http\Controllers\Data\Activity;

use App\Exports\CompanyVehiclesActivityExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Drive;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehicleActivityController extends Controller
{
    function company(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = Activity::byCompanyVehicle()
            ->with('driver_task', 'driver_task.driver', 'user', 'by', 'site');

        $order = $request->get('order');

        if($order == 'past') $q->oldest('time');
        else $q->latest('time');


        if($request->filled('keyword')){
            $q->where(function($q1) use($request){
                $q1->whereHas('driver_task', function($dt) use($request){
                    $dt->whereHas('driver', function($d) use($request){
                        $k = '%'.$request->get('keyword').'%';
                        $d->where('name', 'like', $k);
                    });
                })
                ->orWhereHas('by', function($by) use($request){
                    $k = '%'.$request->get('keyword').'%';
                    $by->where('registration_no', 'like', $k);
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

            $q->whereDate('time', '>=', $from)
                ->whereDate('time', '<=', $to);

            $dates = $from.' to '.$to;
        }

        return response()->view('admin.activity.company_vehicles',[
            'result' => new ResultSet($q, $limit),
            'dates' => $dates
        ]);
    }

    function exportCompany(){
        return new CompanyVehiclesActivityExport();
    }
}
