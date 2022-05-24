<?php

namespace App\Http\Controllers\Data\Vehicles;

use App\Exports\OtherVehicleActivityExport;
use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Drive;
use App\Models\Site;
use App\Models\StaffCheckIn;
use App\Models\Vehicle;
use App\Models\Visit;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;

class SingleVehicleController extends Controller
{
    function get(Request $request, ApiService $api, $vehicle_id){
        $queryParams = [];

        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;
        $queryParams['limit'] = $limit;

        $queryParams['page'] = 1;
        if($request->filled('page')){
            $queryParams['page'] = $request->get('page');
        }

        if($request->filled('keyword')){
            $queryParams['search'] = $request->get('keyword');
        }

        if($request->filled('type')){
            $queryParams['type'] = $request->get('type');
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

            $dates = $from.' to '.$to;

            $queryParams['date'] = $dates;
        }

        $response = $api->get(
            ApiService::ROUTE_GET_SINGLE_VEHICLE,
            ['vehicle_id' => $vehicle_id],
            $queryParams
        );

        if(!$response->wasSuccessful()){
            return back()->withErrors(['status' => $response->message]);
        }

        $data = $response->data;
        $vehicle = new Vehicle($data['vehicle']);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Activity($data);
        });

        if($vehicle->isCompanyVehicle()){
            return $this->getCompany($vehicle, $result, $dates);
        }

        return $this->getOther($vehicle, $result, $dates);
    }

    function getCompany($vehicle, $result, $dates){
        return response()->view('admin.vehicles.single', [
            'vehicle' => $vehicle,
            'result' => $result,
            'dates' => $dates
        ]);
    }

    function getOther($vehicle, $result, $dates){
        return response()->view('admin.vehicles.other_single', [
            'vehicle' => $vehicle,
            'result' => $result,
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
