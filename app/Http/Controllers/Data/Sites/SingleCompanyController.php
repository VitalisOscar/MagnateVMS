<?php

namespace App\Http\Controllers\Data\Sites;

use App\Helpers\ApiResultSet;
use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Site;
use App\Models\Staff;
use App\Models\User;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SingleCompanyController extends Controller
{
    function get(Request $request, ApiService $api, $site_id, $company_id){
        $queryParams = [];

        if($request->filled('keyword')){
            $queryParams['search'] = $request->get('keyword');
        }

        $response = $api->get(ApiService::ROUTE_GET_SINGLE_COMPANY, [
            'site_id' => $site_id,
            'company_id' => $company_id,
        ], $queryParams);

        if(!$response->WasSuccessful()){
            return redirect()->route('admin.sites.single', $site_id)->withErrors(['status' => $response->message]);
        }

        $company = new Company($response->data['company']);
        $company->created_at = $response->data['company']['timestamp'];
        $company->site = new Site($response->data['company']['site']);

        return response()->view('admin.companies.single', [
            'company' => $company,
            'result' => new ApiResultSet($response->getResult(), function($data) use($company){
                $model = new Staff($data);
                $model->created_at = $data['timestamp'];
                $model->company = $company;
                $model->company_id = $company->id;

                return $model;
            })
        ]);
    }

    function delete(Request $request, ApiService $api, $site_id){
        $response = $api->post(ApiService::ROUTE_DELETE_COMPANY, [
            'site_id' => $site_id,
            'company_id' => $request->post('company_id')
        ], [], []);

        if(!$response->wasSuccessful()){
            return back()->withErrors([
                'status' => $response->message
            ]);
        }

        return back()->with([
            'status' => 'Company and staff members have been deleted. Any activity for the deleted company and staff is still preserved'
        ]);
    }
}
