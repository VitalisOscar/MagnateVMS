<?php

namespace App\Http\Controllers\Data\Activity;

use App\Exports\AllStaffCheckInsExport;
use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Company;
use App\Models\Site;
use App\Models\Staff;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffActivityController extends Controller
{
    function __invoke(Request $request, ApiService $api){
        $queryParams = [];

        if($request->filled('company')){
            $queryParams['company'] = $request->get('company');
        }


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


        $response = $api->get(ApiService::ROUTE_GET_ALL_STAFF_ACTIVITY, [], $queryParams);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Activity($data);
        });

        $sites = $this->getSites($api);


        return response()->view('admin.activity.staff', [
                'result' => $result,
                'dates' => $dates,
                'sites' => $sites,
                'companies' => []
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

    function getCompanies($api){
        $response = $api->get(ApiService::ROUTE_GET_SITES);

        $result = new ApiResultSet($response->getResult(), function($data){
            $site = new Site($data);
            $site->created_at = $data['timestamp'];

            return $site;
        });

        return $result->items;
    }

    function getCheckedIn(){
        $query = Staff::whereHas('last_check_in', function($a){
                $a->onDate(Carbon::today())
                    ->atSite(auth('sanctum')->user()->site_id)
                    ->notCheckedOut();
            })
            ->with('company', 'last_activity', 'last_activity.vehicle');

        $result = new ResultSet($query);

        return $this->json->mixed(null, $result->items);
    }

    function export(){
        return new AllStaffCheckInsExport();
    }
}
