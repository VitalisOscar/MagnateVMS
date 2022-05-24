<?php

namespace App\Http\Controllers\Data\Activity;

use App\Exports\CompanyVehiclesActivityExport;
use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Site;
use App\Models\Vehicle;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehicleActivityController extends Controller
{
    function company(Request $request, ApiService $api){
        $queryParams = [];


        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;
        $queryParams['limit'] = $limit;

        $queryParams['page'] = 1;
        if($request->filled('page')){
            $queryParams['page'] = $request->get('page');
        }


        $dates = null;

        if($request->filled('site')){
            $queryParams['site'] = $request->get('site');
        }

        if($request->filled('type')){
            $queryParams['type'] = $request->get('type');
        }

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


        if($request->filled('keyword')){
            $queryParams['search'] = $request->get('keyword');
        }


        $response = $api->get(ApiService::ROUTE_GET_ALL_VEHICLE_ACTIVITY, [], $queryParams);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Activity($data);
        });

        return response()->view('admin.activity.company_vehicles',[
                'result' => $result,
                'dates' => $dates
            ]);
    }

    function getSites($api){
        $response = $api->get(ApiService::ROUTE_GET_SITES);

        $result = new ApiResultSet($response->getResult(), function($data){
            $site = new Site($data);
            $site->created_at = $data['timestamp'];

            return $site;
        });

        return $result->items;
    }

    function getCheckedOut(){
        $query = Vehicle::companyOwned()
            ->whereHas('last_check_out', function($a){
                $a->atSite(auth('sanctum')->user()->site_id)
                    ->notCheckedIn();
            })
            ->with([
                'last_activity', 'last_activity.site', 'last_activity.driver_task',
                'last_activity.driver_task.driver'
            ]);

        $result = new ResultSet($query);

        return $this->json->mixed(null, $result->items);
    }

    function exportCompany(){
        return new CompanyVehiclesActivityExport();
    }
}
