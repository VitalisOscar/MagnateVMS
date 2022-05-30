<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResultSet;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{

    function __invoke(Request $request, ApiService $api){
        $queryParams = [];

        $sites = $this->getSites($api);

        $queryParams['date'] = $request->get('date') ?? Carbon::today()->format('Y-m-d');
        $queryParams['site'] = $request->get('site') ?? $sites[0]->id ?? null;

        $response = $api->get(ApiService::ROUTE_GET_DAILY_SITE_STATS, [], $queryParams);

        // if(!$response->WasSuccessful()){
        //     return redirect()->route('admin.sites.add')->withErrors([
        //         'status' => $response->message,
        //     ]);
        // }

        $stat = $response->data['stats'] ?? null;
        $totals = $response->data['totals'] ?? null;

        if($totals == null){
            $totals = [
                'sites' => 0,
                'users' => 0,
                'visitors' => 0
            ];
        }

        $activity_data = [];

        for($i = 0; $i<24; $i++){
            $hour_activity['visitors'] = $stat['hours']['visitors'][$i] ?? 0;
            $hour_activity['staff'] = $stat['hours']['staff'][$i] ?? 0;
            $hour_activity['vehicles'] = $stat['hours']['vehicles'][$i] ?? 0;

            $activity_data[$i] = $hour_activity;
        }

        $summaries = [
            [
                'label' => 'Visitors',
                'value' => $stat['visitors'] ?? 0,
                'color' => '#2dce89'
            ],
            [
                'label' => 'Staff',
                'value' => $stat['staff'] ?? 0,
                'color' => '#5e72e4'
            ],
            [
                'label' => 'Company Vehicles',
                'value' => $stat['company_vehicles'] ?? 0,
                'color' => 'coral'
            ],
        ];

        return view('admin.dashboard', [
            'activity_data' => $activity_data,
            'summaries' => $summaries,
            'totals' => $totals,
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

    /**
     * @param Builder $query
     */
    function getSql($query){
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function($binding){
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

}
