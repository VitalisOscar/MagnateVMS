<?php

namespace App\Http\Controllers\Data\Users;

use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetUsersController extends Controller
{
    function __invoke(Request $request, ApiService $api){
        $queryParams = [];

        if($request->filled('keyword')){
            $queryParams['search'] = $request->get('keyword');
        }

        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;
        $queryParams['limit'] = $limit;

        $queryParams['page'] = 1;
        if($request->filled('page')){
            $queryParams['page'] = $request->get('page');
        }


        $response = $api->get(ApiService::ROUTE_GET_USERS, [], $queryParams);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new User($data);
        });

        return response()->view('admin.users.all',[
            'result' => $result
        ]);
    }
}
