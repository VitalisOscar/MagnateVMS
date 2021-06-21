<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Exports\OtherVehicleActivityExport;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
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

        if($vehicle->vehicleable_type){
            return $this->getOther($request, $vehicle);
        }

        return $this->getCompany($request, $vehicle);
    }

    function getCompany(Request $request, $vehicle){
        // Activity
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = Drive::whereVehicleId($vehicle->id)
            ->with('driveable_in', 'driveable_out');

        $order = $request->get('order');

        if($order == 'past') $q->oldest('time_in');
        else $q->latest('time_in');


        if($request->filled('keyword')){
            $q->where(function($q1) use($request){
                $q1->orWhereHas('driveable_in', function($q2) use($request){
                    $k = '%'.$request->get('keyword').'%';
                    $q2->where('name', 'like', $k);
                })
                ->orWhereHas('driveable_out', function($q2) use($request){
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
        // Activity

        return response()->view('admin.vehicles.single', [
            'vehicle' => $vehicle,
            'result' => new ResultSet($q, $limit),
            'dates' => $dates
        ]);
    }

    function getOther(Request $request, $vehicle){
        if($vehicle->vehicleable_type == 'staff'){
            $q = StaffCheckIn::whereHas('vehicle', function($q1) use ($vehicle){
                $q1->whereId($vehicle->id);
            });
        }else{
            $q = Visit::whereHas('vehicle', function($q1) use ($vehicle){
                $q1->whereId($vehicle->id);
            });
        }

        $q->with('site', 'check_in_user', 'check_out_user');

        // Activity
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;


        $order = $request->get('order');

        if($order == 'past') $q->oldest('time_in');
        else $q->latest('time_in');


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
