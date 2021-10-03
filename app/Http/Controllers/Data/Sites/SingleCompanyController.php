<?php

namespace App\Http\Controllers\Data\Sites;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Site;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class SingleCompanyController extends Controller
{
    function get(Request $request, $site_id, $company_id){
        $company = Company::whereId($company_id)
            ->whereSiteId($site_id)
            ->withCount('staff')
            ->first();

        if($company == null){
            return redirect()->route('admin.sites.single', $site_id);
        }

        $limit = intval($request->get('limit'));

        if(!in_array($limit, [15,25,50,100,250])){
            $limit = 15;
        }

        return response()->view('admin.companies.single', [
            'company' => $company,
            'result' => new ResultSet($company->staff(), $limit)
        ]);
    }
}
