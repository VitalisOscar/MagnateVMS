<?php

namespace App\Http\Controllers\Data\Staff;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Site;
use App\Models\Staff;
use Exception;
use Illuminate\Http\Request;

class GetStaffController extends Controller
{
    function __invoke(Request $request, $site_id, $company_id){
        $company = Company::whereId($company_id)
            ->whereSiteId($site_id)
            ->with('site')
            ->first();

        if($company == null)
        return redirect()
            ->route('admin.sites.single', $site_id)
            ->withErrors(['status' => 'Company not found']);

        if($request->isMethod('GET')){
            return response()->view('admin.staff.add', [
                'company' => $company
            ]);
        }

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

    function atSite(Request $request){
        $site = Site::where('id', auth('sanctum')->user()->site_id)->first();

        if($site == null){
            $result = ResultSet::empty();
        }else{

            $limit = 25;

            $q = $site->staff();

            if($request->filled('keyword')){
                $k = "%".$request->get('keyword')."%";
                $q->where(function($s) use($k){
                    $s->where('staff.name', 'like', $k)
                        ->orWhere('phone', 'like', $k)
                        ->orWhere('extension', 'like', $k);
                });
            }

            $result = new ResultSet($q, $limit);
        }

        return $this->json->mixed($result, $result->items);
    }
}
