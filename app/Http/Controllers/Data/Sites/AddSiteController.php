<?php

namespace App\Http\Controllers\Data\Sites;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;

class AddSiteController extends Controller
{
    function __invoke(Request $request, ApiService $api){
        $validator = validator($request->post(), [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        try{
            $response = $api->post(ApiService::ROUTE_ADD_SITE, [], [], [
                'name' => $request->post('name')
            ]);

            if($response->wasSuccessful()){
                return redirect()
                    ->route('admin.sites')
                    ->with(['status' => 'Site has been added']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
