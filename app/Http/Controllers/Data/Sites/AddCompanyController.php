<?php

namespace App\Http\Controllers\Data\Sites;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;

class AddCompanyController extends Controller
{
    function __invoke(Request $request, ApiService $api, $site_id){
        $response = $api->get(ApiService::ROUTE_GET_SINGLE_SITE, ['site_id' => $site_id]);

        if(!$response->WasSuccessful()){
            return redirect()->route('admin.sites')->withErrors(['status' => $response->message]);
        }

        $site = new Site($response->data['site']);
        $site->created_at = $response->data['site']['timestamp'];

        if($request->isMethod('GET')){
            return response()->view('admin.companies.add', [
                'site' => $site
            ]);
        }

        // Submitted for adding
        $v = validator($request->post(), [
            'name' => 'required|string'
        ], [
            'name.required' => 'Please enter the company name',
            'name.string' => 'Please enter a valid company name',
        ]);

        if($v->fails()){
            return back()->withInput()->withErrors($v->errors());
        }


        try{
            $response = $api->post(
                ApiService::ROUTE_ADD_COMPANY,
                ['site_id' => $site_id],
                [],
                [
                    'name' => $request->post('name')
                ] // Data
            );

            if($response->wasSuccessful()){
                return redirect()
                    ->route('admin.sites.single', ['site_id' => $site_id])
                    ->with(['status' => $request->post('name').' has been added to the site']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
