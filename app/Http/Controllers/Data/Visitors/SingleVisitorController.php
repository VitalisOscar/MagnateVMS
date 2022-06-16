<?php

namespace App\Http\Controllers\Data\Visitors;

use App\Exports\SingleVisitorExport;
use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Company;
use App\Models\Site;
use App\Models\Visitor;
use App\Services\ApiService;
use Illuminate\Http\Request;

class SingleVisitorController extends Controller
{
    function get(Request $request, ApiService $api, $visitor_id){
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


        $response = $api->get(
            ApiService::ROUTE_GET_SINGLE_VISITOR_ACTIVITY,
            ['visitor_id' => $visitor_id],
            $queryParams
        );

        if(!$response->WasSuccessful()){
            return redirect()->route('admin.activity.visitors')->withErrors(['status' => $response->message]);
        }

        dump($response);
        return;

        $result = new ApiResultSet($response->getResult(), function($data){
            return new Activity($data);
        });

        $visitor = new Visitor($response->data['visitor']);

        $sites = $this->getSites($api);

        return response()->view('admin.visitors.single', [
            'visitor' => $visitor,
            'result' => $result,
            'dates' => $dates,
            'sites' => $sites
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

    function getByIdNumber(Request $request){
        $visitor = Visitor::where('id_number', $request->get('id_number'))->first();

        return $this->json->data([
            'visitor' => $visitor,
            'vehicles' => $visitor ? $visitor->vehicles()->get() : [],
            'companies' => Company::where('site_id', $request->user('sanctum')->site_id)->get()
        ]);
    }

    function export($visitor_id){
        return new SingleVisitorExport($visitor_id);
    }
}
