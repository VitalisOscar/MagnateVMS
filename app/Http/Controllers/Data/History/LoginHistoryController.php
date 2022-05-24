<?php

namespace App\Http\Controllers\Data\History;

use App\Exports\LoginsExport;
use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\Site;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
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

        if($request->filled('site')){
            $queryParams['site'] = $request->get('site');
        }

        if($request->filled('user')){
            $queryParams['user'] = $request->get('user');
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

        $response = $api->get(ApiService::ROUTE_GET_LOGINS, [], $queryParams);

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Login($data);
        });


        return response()->view('admin.history.logins',[
            'result' => $result,
            'dates' => $dates,
            'sites' => $this->getSites($api)
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

    function export(){
        return new LoginsExport();
    }
}
