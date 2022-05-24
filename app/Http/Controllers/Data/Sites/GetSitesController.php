<?php

namespace App\Http\Controllers\Data\Sites;

use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\ApiService;
use Illuminate\Http\Request;

class GetSitesController extends Controller
{
    function __invoke(Request $request, ApiService $api){
        $response = $api->get(ApiService::ROUTE_GET_SITES);

        $result = new ApiResultSet($response->getResult(), function($data){
            $site = new Site($data);
            $site->created_at = $data['timestamp'];

            return $site;
        });

        return response()->view('admin.sites.all',[
            'result' => $result
        ]);
    }

    function get(Request $request){
        $q = Site::query()
            ->withCount(['companies', 'staff']);

        $data = new ResultSet($q);

        return $request->is('api*') ?
            $this->json->mixed($data, $data->items) :
            response()->view('admin.sites.all',[
                'result' => $data
            ]);
    }
}
