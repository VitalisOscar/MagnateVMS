<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use App\Imports\StaffImport;
use App\Models\Company;
use App\Models\Staff;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StaffController extends Controller
{
    function getForAdd($site_id, $company_id){
        $company = Company::whereId($company_id)
            ->whereSiteId($site_id)
            ->with('site')
            ->first();

        if($company == null)
        return redirect()
            ->route('admin.sites.single', $site_id)
            ->withErrors(['status' => 'Company not found']);

        return response()->view('admin.staff.add', [
            'company' => $company
        ]);
    }

    function add(Request $request, $site_id, $company_id){
        $company = Company::whereId($company_id)
            ->whereSiteId($site_id)
            ->first();

        if($company == null)
        return back()
            ->withErrors(['status' => 'Company not found']);

        $validator = validator($request->post(), [
            'name' => 'required|string',
            'phone' => 'required|regex:/0([0-9]){9}/'
        ],[
            'phone.required' => 'Please provide a phone number for the staff member',
            'phone.regex' => 'Enter a valid 10 digit phone number with no country code'
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator->errors());
        }

        $staff = new Staff([
            'name' => $request->post('name'),
            'phone' => $request->post('phone'),
            'company_id' => $company_id
        ]);

        try{
            if($staff->save()){
                return redirect()
                    ->route('admin.sites.company', ['site_id' => $site_id, 'company_id' => $company->id])
                    ->with(['status' => $staff->name.' has been added to staff members']);
            }
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
