<?php

namespace App\Http\Controllers\Api\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Drive;
use App\Models\Vehicle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleCheckInController extends Controller
{
    function __invoke(Request $request){
        $rules = [
            'driver_type' => 'required|in:staff,driver',
            'driver_id' => 'required|exists:'.(($request->post('driver_type') == 'staff') ? 'staff,id':'drivers,id'),
            'vehicle_id' => 'required|exists:vehicles,id',
            'mileage' => 'required|numeric',
            'fuel' => 'required|numeric',
        ];

        $validator = Validator::make($request->post(), $rules,[
            'driver_type.required' => 'You need to specify if the driver is a company driver or other staff member',
            'driver_type.in' => 'You need to specify if the driver is a company driver or other staff member',
            'driver_id.required' => 'Please select a driver or staff with the vehicle',
            'driver_id.exists' => 'Please select a driver or staff with the vehicle',
            'vehicle_id.required' => 'Please select a vehicle',
            'vehicle_id.exists' => 'Please select a vehicle',
        ]);

        if($validator->fails()) return $this->json->error($validator->errors()->first(), $validator->errors()->all());

        // Get vehicle
        $vehicle = Vehicle::whereId($request->post('vehicle_id'))->first();

        // We are only checking in company vehicles
        if(!$vehicle->isCompanyVehicle()){
            return $this->json->error('Please select a company vehicle');
        }

        $drive = $vehicle->last_drive;

        // Check if the drive exists, and has not been checked in
        // maybe the check out was through manual methods
        if($drive == null || $drive->isCheckedIn()){
            $drive = new Drive();
            $drive->vehicle_id = $vehicle->id;

            $drive->mileage_out = null;
            $drive->fuel_out = null;
            $drive->time_out = null;
        }

        $drive->mileage_in = $request->post('mileage');
        $drive->fuel_in = $request->post('fuel');
        $drive->time_in = Carbon::now()->toDateTimeString();

        // driver
        $drive->driveable_in_id = $request->post('driver_id');

        if($request->post('driver_type') == 'staff'){
            $drive->driveable_in_type = 'staff';
        }else{
            $drive->driveable_in_type = 'driver';
        }

        // user
        $drive->checked_in_by = auth('sanctum')->id();

        try{
            if($drive->save()) return $this->json->success('The vehicle has been checked in');

            return $this->json->error('Something went wrong. Please try again or check out the vehicle manually and contact IT');
        }catch(Exception $e){
            return $this->json->error($e->getMessage().'Something went wrong. Please try again or check out the vehicle manually and contact IT');
        }
    }
}
