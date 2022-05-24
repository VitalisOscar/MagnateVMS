<?php

namespace App\Http\Controllers\Data\Staff;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Site;
use App\Models\Staff;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;

class SingleStaffController extends Controller
{
    function get(ApiService $api, $site_id, $company_id, $staff_id){
        $response = $api->get(ApiService::ROUTE_GET_SINGLE_STAFF, [
            'site_id' => $site_id,
            'company_id' => $company_id,
            'staff_id' => $staff_id,
        ]);

        if(!$response->WasSuccessful()){
            return redirect()
                ->route('admin.sites.company', ['site_id' => $site_id, 'company_id' => $company_id])
                ->withErrors(['status' => 'Staff member not found. Might have been deleted or url is incorrect']);
        }

        $staff = new Staff($response->data['staff']);
        $staff->created_at = $response->data['staff']['timestamp'];
        $company = new Company($response->data['staff']['company']);
        $site = new Site($response->data['staff']['company']['site']);

        $company->site = $site;
        $staff->company = $company;

        return response()->view('admin.staff.single', [
            'staff' => $staff
        ]);
    }

    function update(Request $request, ApiService $api, $site_id, $company_id, $staff_id){
        $validator = validator($request->post(), [
            'name' => 'required|string',
            'phone' => 'nullable|regex:/0([0-9]){9}/',
            'extension' => 'nullable|string',
            'department' => 'required|string'
        ],[
            'phone.required' => 'Please provide a phone number for the staff member',
            'phone.regex' => 'Enter a valid 10 digit phone number with no country code'
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator->errors());
        }


        $response = $api->post(ApiService::ROUTE_UPDATE_STAFF, [
            'site_id' => $site_id,
            'company_id' => $company_id,
            'staff_id' => $staff_id,
        ], [], [
            'name' => $request->post('name'),
            'phone' => $request->post('phone'),
            'extension' => $request->post('extension'),
            'department' => $request->post('department')
        ]);

        if(!$response->WasSuccessful()){
            return back()->withInput()
                ->withErrors(['status' => $response->message]);
        }

        return back()
            ->with(['status' => 'Staff info has been updated']);
    }

    function delete(Request $request, ApiService $api, $site_id, $company_id, $staff_id){
        $response = $api->post(ApiService::ROUTE_DELETE_STAFF, [
            'site_id' => $site_id,
            'company_id' => $company_id,
            'staff_id' => $staff_id,
        ], [], []);

        if(!$response->wasSuccessful()){
            return back()->withErrors([
                'status' => $response->message
            ]);
        }

        return back()->with([
            'status' => 'Staff member record deleted. Any activity for the staff is still preserved'
        ]);
    }
}
