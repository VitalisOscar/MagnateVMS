<?php

namespace App\Http\Controllers\Data\Vehicles;

use App\Exports\OtherVehicleActivityExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Drive;
use App\Models\Site;
use App\Models\StaffCheckIn;
use App\Models\Vehicle;
use App\Models\Visit;
use Exception;
use Illuminate\Http\Request;

class SingleVehicleController extends Controller
{
    function get(Request $request, $vehicle_id){
        $vehicle = Vehicle::whereId($vehicle_id)
            ->first();

        if($vehicle == null){
            return redirect()->route('admin.vehicles');
        }

        if($vehicle->isCompanyVehicle()){
            return $this->getCompany($request, $vehicle);
        }

        return $this->getOther($request, $vehicle);
    }

    function getCompany(Request $request, $vehicle){
        // Activity
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = $vehicle->activities()
            ->with('driver_task', 'driver_task.driver', 'user', 'site');

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

        return response()->view('admin.vehicles.single', [
            'vehicle' => $vehicle,
            'result' => new ResultSet($q, $limit),
            'dates' => $dates
        ]);
    }

    function getOther(Request $request, $vehicle){
        $q = Activity::whereHas('vehicle', function($v) use($vehicle){
                $v->whereId($vehicle->id);
            })
            ->with('by', 'user', 'site');

        // Activity
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;


        $order = $request->get('order');

        if($order == 'past') $q->oldest('time');
        else $q->latest('time');


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
        // Activity

        return response()->view('admin.vehicles.other_single', [
            'vehicle' => $vehicle,
            'result' => new ResultSet($q, $limit),
            'dates' => $dates
        ]);
    }

    function update(Request $request, $site_id){
    }

    function exportOtherActivity($vehicle_id){
        $vehicle = Vehicle::whereId($vehicle_id)
            ->first();

        if($vehicle == null || $vehicle->isCompanyVehicle()){
            return back()->withErrors(['status' => 'Vehicle not found']);
        }

        return new OtherVehicleActivityExport($vehicle);
    }
}
