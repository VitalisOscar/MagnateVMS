<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffCheckIn;
use App\Services\VehicleService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class StaffCheckInController extends Controller
{
    function __invoke(Request $request, VehicleService $vehicleService){
        $validator = validator($request->post(), [
            'staff_id' => 'required|exists:staff,id'
        ], [
            'staff_id.required' => 'Please use the provided check in interface on the app to check in staff members',
            'staff_id.exists' => 'Please use the provided check in interface on the app to check in staff members',
        ]);

        if($validator->fails()) return $this->json->error($validator->errors()->first());

        // Get the staff
        $staff = Staff::where('id', $request->post('staff_id'))->first();

        if($staff->isCheckedIn()) return $this->json->success('From our records, '.$staff->name.' is still checked in at this site and has not been checked out via the app');

        $vehicle_id = null;
        if($request->filled('car_registration')){
            $vehicle = $vehicleService->getOrAddVehicle($request->post('car_registration'), 'staff', $staff->id);

            if($vehicle == null) return $this->json->error('Something went wrong and we cannot check in for now. Please retry or report to admin');
            $vehicle_id = $vehicle->id;
        }

        // Create a check in
        $checkin = new StaffCheckIn([
            'checked_in_by' => auth('sanctum')->id(),
            'staff_id' => $request->post('staff_id'),
            'time_in' => Carbon::now()->toDateTimeString(),
            'site_id' => auth('sanctum')->user()->site_id,
            'vehicle_id' => $vehicle_id,
        ]);

        try{
            if($checkin->save()) return $this->json->data($staff->name.' has been checked in and can proceed', $checkin);
        }catch(Exception $e){}

        return $this->json->error('Something went wrong and we cannot check in for now. Please retry or report to admin');
    }
}
