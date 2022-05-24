<?php

namespace App\Http\Controllers\Data\Sites;

use App\Helpers\ApiResultSet;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Site;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;

class SingleSiteController extends Controller
{
    function get(ApiService $api, $site_id){
        $response = $api->get(ApiService::ROUTE_GET_SINGLE_SITE, ['site_id' => $site_id]);

        if(!$response->WasSuccessful()){
            return redirect()->route('admin.sites')->withErrors(['status' => $response->message]);
        }

        $site = new Site($response->data['site']);
        $site->created_at = $response->data['site']['timestamp'];

        $companies = [];

        foreach($response->getItems() ?? [] as $data){
            $model = new Company($data);
            array_push($companies, $model);
        }

        $site->companies = $companies;

        return response()->view('admin.sites.single', [
                'site' => $site
            ]);
    }

    function update(Request $request, ApiService $api, $site_id){
        $v = validator($request->post(), [
            'name' => 'required|string'
        ], [
            'name.required' => 'Please enter the site name',
            'name.string' => 'Please enter a valid site name',
        ]);

        if($v->fails()){
            return back()->withInput()->withErrors($v->errors());
        }
        
        $response = $api->post(ApiService::ROUTE_UPDATE_SITE, ['site_id' => $site_id], [], [
            'name' => $request->post('name')
        ]);

        if($response->wasSuccessful()){
            return back()->with(['status' => 'Site name has been updated']);
        }

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
