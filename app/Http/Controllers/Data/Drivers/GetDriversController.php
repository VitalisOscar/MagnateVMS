<?php

namespace App\Http\Controllers\Data\Drivers;

use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Services\ApiService;
use Illuminate\Http\Request;

class GetDriversController extends Controller
{
    function __invoke(Request $request, ApiService $api){
        $queryParams = [];

        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;
        $queryParams['limit'] = $limit;

        $queryParams['page'] = 1;
        if($request->filled('page')){
            $queryParams['page'] = $request->get('page');
        }

        $response = $api->get(ApiService::ROUTE_GET_DRIVERS, [], $queryParams);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Driver($data);
        });

        return response()->view('admin.drivers.all',[
            'result' => $result
        ]);
    }

    function all(Request $request){
        $result = new ResultSet(Driver::query());

        return $this->json->mixed(null, $result->items);
    }
}
