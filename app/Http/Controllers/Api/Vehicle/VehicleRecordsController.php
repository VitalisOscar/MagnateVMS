<?php

namespace App\Http\Controllers\Api\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffCheckIn;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleRecordsController extends Controller
{
    function __invoke(Request $request){
        $date = $request->filled('date') ?
            $request->get('date'):
            Carbon::today()->format('Y-m-d');

        $from_visits = Visit::query()
            ->select(['time_in', 'time_out', 'vehicle_id'])
            ->atSite()
            ->whereHas('vehicle')
            ->whereDate('time_in', $date)
            ->with('vehicle', 'vehicle.vehicleable');

        $from_staff = StaffCheckIn::query()
            ->select(['time_in', 'time_out', 'vehicle_id'])
            ->atSite()
            ->whereHas('vehicle')
            ->whereDate('time_in', $date)
            ->with('vehicle', 'vehicle.vehicleable');

        $q = $from_staff->unionAll($from_visits)
            ->orderBy('time_in', 'desc');

        $total = $q->count();

        // paginate
        $page = $request->filled('page') ?
            intval($request->get('page')) : 1;

        $limit = 15;
        $offset = $limit * ($page - 1);

        $q->limit($limit)->offset($offset);

        $last_page = ceil($total/$limit);


        $res = $q->get()->each(function($activity){
            $vehicle = $activity->vehicle;

            if($vehicle->vehicleable_type == 'visitor'){
                $activity->driver_type = 'Visitor';
            }else if($vehicle->vehicleable_type == 'staff'){
                $activity->driver_type = 'Staff';
            }

            $activity->driver_name = $vehicle->vehicleable->name;

            $activity->car_registration = $vehicle->registration_no;

            $activity->visit_date = $activity->fmtDate;

            $in = Carbon::createFromTimeString($activity->time_in);

            $activity->time_in = $in->format('H:i');

            $activity->time_out = $activity->time_out ?
                Carbon::createFromTimeString($activity->time_out)->format('H:i') :
                null;
        });

        return $this->json->mixed([
            'total' => $total,
            'last_page' => $last_page,
            'date' => $date
        ], $res);
    }
}
