<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffCheckIn;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class StaffCheckOutController extends Controller
{
    function __invoke(Request $request){
        $validator = validator($request->post(), [
            'staff_id' => 'required|exists:staff,id'
        ], [
            'staff_id.required' => 'Please use the provided check out buttons on the app to check out a staff',
            'staff_id.exists' => 'Please use the provided check out buttons on the app to check out a staff',
        ]);

        if($validator->fails()) return $this->json->error($validator->errors()->first());

        // Get the staff
        $staff = Staff::where('id', $request->post('staff_id'))->first();

        // Get the check in
        $checkin = $staff->check_ins()
            ->latest('time_in')
            ->stillIn() // not checked out
            // ->today() // checked in today
            ->atSite() // same site as user
            ->first();

        // doubt if it will get to this point,
        if($checkin == null) return $this->json->error($staff->name.' has not checked in at this site from the app recently, or has already been checked out');

        // check out
        $checkin->time_out = Carbon::now()->toDateTimeString();
        $checkin->checked_out_by = auth('sanctum')->id();

        try{
            if($checkin->save()) return $this->json->success($staff->name.' has been checked out and can go out');
        }catch(Exception $e){}

        return $this->json->error('Something went wrong and we cannot check out for now. Please retry or report to admin');
    }
}
