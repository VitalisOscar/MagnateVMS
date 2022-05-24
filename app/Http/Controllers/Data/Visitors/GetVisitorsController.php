<?php

namespace App\Http\Controllers\Data\Visitors;

use App\Exports\AllVisitorsExport;
use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Services\ApiService;
use Illuminate\Http\Request;

class GetVisitorsController extends Controller
{
    function __invoke(Request $request, ApiService $api){
        $queryParams = [];

        $order = $request->get('order');
        if(!in_array($order, ['recent', 'az', 'za'])) $order = 'recent';
        $queryParams['order'] = $order;

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

        $response = $api->get(ApiService::ROUTE_GET_VISITORS, [], $queryParams);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Visitor($data);
        });

        return response()->view('admin.visitors.all',[
            'result' => $result
        ]);
    }

    function export(){
        return new AllVisitorsExport();
    }
}
