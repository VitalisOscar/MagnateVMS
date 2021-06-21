<?php

namespace App\Http\Controllers\Admin\Sites;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Site;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class SingleCompanyController extends Controller
{
    function get($site_id, $company_id){
        $company = Company::whereId($company_id)
            ->whereSiteId($site_id)
            ->withCount('staff')
            ->first();

        if($company == null){
            return redirect()->route('admin.sites.single', $site_id);
        }

        return response()->view('admin.companies.single', [
            'company' => $company,
            'result' => new ResultSet($company->staff(), 15)
        ]);
    }

    function addCompany(Request $request, $site_id){
        $v = validator($request->post(), [
            'name' => 'required|string'
        ], [
            'name.required' => 'Please enter the company name',
            'name.string' => 'Please enter a valid company name',
        ]);

        if($v->fails()){
            return back()->withInput()->withErrors($v->errors());
        }

        $company = new Company([
            'name' => $request->post('name'),
            'site_id' => $site_id
        ]);

        try{
            if($company->save()){
                return back()
                    ->with(['status' => 'Company has been added to the site']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
