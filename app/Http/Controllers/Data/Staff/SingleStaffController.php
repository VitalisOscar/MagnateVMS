<?php

namespace App\Http\Controllers\Data\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Exception;
use Illuminate\Http\Request;

class SingleStaffController extends Controller
{
    function get($site_id, $company_id, $staff_id){
        $staff = Staff::whereId($staff_id)
            ->whereCompanyId($company_id)
            ->with('company', 'company.site')
            ->first();

        if($staff == null)
        return redirect()
            ->route('admin.sites.company', ['site_id' => $site_id, 'company_id' => $company_id])
            ->withErrors(['status' => 'Staff member not found. Might have been deleted or url is incorrect']);

        return response()->view('admin.staff.single', [
            'staff' => $staff
        ]);
    }

    function update(Request $request, $site_id, $company_id, $staff_id){
        $staff = Staff::whereId($staff_id)
            ->whereCompanyId($company_id)
            ->first();

        if($staff == null)
        return back()
            ->withErrors(['status' => 'Staff member not found. Might have been deleted or url is incorrect']);

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

        if($staff->name == $request->post('name') &&
            $staff->phone == $request->post('phone') &&
            $staff->extension == $request->post('extension') &&
            $staff->department == $request->post('department')
        ){
            return back()
                ->withInput()
                ->with(['status' => 'No changes have been made to be saved']);
        }

        $staff->name = $request->post('name');
        $staff->phone = $request->post('phone');
        $staff->extension = $request->post('extension');
        $staff->department = $request->post('department');

        try{
            if($staff->save()){
                return back()
                    ->with(['status' => 'Staff info has been updated']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }

    function delete(Request $request, $site_id, $company_id, $staff_id){
        $staff = Staff::whereId($staff_id)
            ->whereCompanyId($company_id)
            ->first();

        if($staff == null)
        return back()
            ->withErrors(['status' => 'Staff member not found. Might have been deleted or url is incorrect']);

        try{
            if(!$staff->delete()){
                return back()
                    ->withErrors(['status' => 'Unable to delete staff member. Please retry']);
            }

            return back();
        }catch(Exception $e){}

        return back()
            ->withErrors(['status' => 'Unable to delete staff member. PLease retry']);
    }
}
