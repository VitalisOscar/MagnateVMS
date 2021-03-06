<?php

namespace App\Http\Controllers\Data\Staff;

use App\Http\Controllers\Controller;
use App\Imports\StaffImport;
use App\Models\Company;
use App\Models\Site;
use App\Models\Staff;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AddStaffController extends Controller
{
    function __invoke(Request $request, ApiService $api, $site_id, $company_id){
        $response = $api->get(ApiService::ROUTE_GET_SINGLE_COMPANY, [
            'site_id' => $site_id,
            'company_id' => $company_id
        ]);

        if(!$response->WasSuccessful()){
            return redirect()->route('admin.sites.single', $site_id)->withErrors(['status' => $response->message]);
        }

        $company = new Company($response->data['company']);
        $company->site = new Site($response->data['company']['site']);
        $company->created_at = $response->data['company']['timestamp'];

        if($request->isMethod('GET')){
            return response()->view('admin.staff.add', [
                'company' => $company
            ]);
        }

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

        try{
            $response = $api->post(ApiService::ROUTE_ADD_STAFF, [
                'site_id' => $site_id,
                'company_id' => $company_id
            ], [], [
                'name' => $request->post('name'),
                'phone' => $request->post('phone'),
                'extension' => $request->post('extension'),
                'department' => $request->post('department'),
                'company_id' => $company_id
            ]);

            if($response->wasSuccessful()){
                return redirect()
                    ->route('admin.sites.company', ['site_id' => $site_id, 'company_id' => $company->id])
                    ->with(['status' => 'Staff member record has been added']);
            }

            return back()
                ->withInput()
                ->withErrors(['status' => $response->message]);
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }

    function import(Request $request, $site_id, $company_id){
        $company = Company::whereId($company_id)
            ->whereSiteId($site_id)
            ->first();

        if($company == null)
        return back()
            ->withErrors(['status' => 'Company not found']);

        $validator = validator($request->file(), [
            'import' => 'required|file',
        ],[
            'import.required' => 'Please upload a file with the staff members to import',
            'import.file' => 'Please upload a file with the staff members to import'
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator->errors());
        }

        try{
            Excel::import(new StaffImport($company_id), $request->file('import'));

            return redirect()->route('admin.sites.company', [
                'company_id' => $company_id,
                'site_id' => $site_id,
            ])->with([
                'status' => 'Staff will be imported from the file'
            ]);
        }catch(Exception $e){
            return back()->withInput()->withErrors(['status' => $e->getMessage().'Oops. Something went wrong']);
        }

    }
}
