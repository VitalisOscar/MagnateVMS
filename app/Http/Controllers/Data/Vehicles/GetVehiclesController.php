<?php

namespace App\Http\Controllers\Data\Vehicles;

use App\Exports\VehiclesExport;
use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetVehiclesController extends Controller
{
    function company(Request $request, ApiService $api){
        // $limit = intval($request->get('limit'));
        // if(!in_array($limit, [15,30,50,100])) $limit = 15;

        // if($request->is('api*')) $limit = null;

        // $order = $request->get('order');

        // $q = Vehicle::companyOwned();

        // if($request->filled('keyword')){
        //     $k = '%'.$request->get('keyword').'%';

        //     $q->where(function($q1) use($k){
        //         $q1->where('registration_no', 'like', $k)
        //             ->orWhere('description', 'like', $k);
        //     });
        // }

        // if($order == 'az') $q->orderBy('registration_no', 'ASC');
        // elseif($order == 'za') $q->orderBy('registration_no', 'DESC');

        // $result = new ResultSet($q, $limit);

        // return $request->is('api*') ?
        //     $this->json->mixed(null, $result->items)
        //     : response()->view('admin.vehicles.company',[
        //         'result' => $result,
        //     ]);

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

        $response = $api->get(ApiService::ROUTE_GET_COMPANY_VEHICLES, [], $queryParams);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Vehicle($data);
        });

        return response()->view('admin.vehicles.company',[
            'result' => $result
        ]);
    }

    function other(Request $request, ApiService $api){
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

        $show = $request->get('show');

        if($show == 'staff') $queryParams['owner'] = 'Staff';
        elseif($show == 'visitors') $queryParams['owner'] = 'Visitor';

        $response = $api->get(ApiService::ROUTE_GET_NON_COMPANY_VEHICLES, [], $queryParams);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Vehicle($data);
        });

        return response()->view('admin.vehicles.others',[
            'result' => $result
        ]);
    }

    function exportOther(){
        return new VehiclesExport("other");
    }

    function exportCompany(){
        return new VehiclesExport('company');
    }
}
