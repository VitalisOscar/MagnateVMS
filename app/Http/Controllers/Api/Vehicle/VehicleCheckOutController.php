<?php

namespace App\Http\Controllers\Api\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Drive;
use App\Models\Vehicle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleCheckOutController extends Controller
{
    function __invoke(Request $request)
    {
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

        // We are only checking out company vehicles
        if(!$vehicle->isCompanyVehicle()){
            return $this->json->error('Please select a company vehicle');
        }

        // Todo
        // if(!$vehicle->isOut()){
        //     return $this->json->error('The selected vehicle '.$vehicle->registration_no.' has already been checked out today. No one has checked it back in');
        // }

        // if($drive != null) return $this->json->error('Seems like the vehicle, '.$request->post('car').' has already been checked out from the app today');

        $drive = new Drive();
        $drive->mileage_out = $request->post('mileage');
        $drive->fuel_out = $request->post('fuel');
        $drive->time_out = Carbon::now()->toDateTimeString();
        $drive->vehicle_id = $request->post('vehicle_id');

        // driver
        $drive->driveable_out_id = $request->post('driver_id');

        if($request->post('driver_type') == 'staff'){
            $drive->driveable_out_type = 'staff';
        }else{
            $drive->driveable_out_type = 'driver';
        }

        // user
        $drive->checked_out_by = auth('sanctum')->id();

        try{
            if($drive->save()) return $this->json->success('Done. The vehicle check out details have been saved');

            return $this->json->error('Something went wrong. Please try again or check out the vehicle manually and contact IT');
        }catch(Exception $e){
            return $this->json->error($e->getMessage().'Something went wrong. Please try again or check out the vehicle manually and contact IT');
        }
    }
}
