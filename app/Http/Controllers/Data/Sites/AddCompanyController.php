<?php

namespace App\Http\Controllers\Data\Sites;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Exception;
use Illuminate\Http\Request;

class AddCompanyController extends Controller
{
    function __invoke(Request $request, $site_id){
        $site = Site::whereId($site_id)
            ->first();

        if($site == null){
            return redirect()->route('admin.sites');
        }

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

        $company = $site->companies()->create([
            'name' => $request->post('name'),
        ]);

        try{
            if($company != null && $company->id != null){
                return redirect()
                    ->route('admin.sites.company', ['site_id' => $site_id, 'company_id' => $company->id])
                    ->with(['status' => 'Company has been added to the site']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
