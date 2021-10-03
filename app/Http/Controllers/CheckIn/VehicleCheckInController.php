<?php

namespace App\Http\Controllers\CheckIn;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\DriverActivity;
use App\Models\Staff;
use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehicleCheckInController extends Controller
{
    function __invoke(Request $request)
    {
        $validator = Validator::make(array_merge($request->post(), $request->file()), [
            'driver_id' => 'required|exists:staff,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'mileage' => 'required|numeric',
            'task' => 'required|string',
        ]);

        if($validator->fails()){
            return $this->json->errors($validator->errors()->all());
        }

        // Get vehicle
        $vehicle = Vehicle::companyOwned()->whereId($request->post('vehicle_id'))->first();

        if(!$vehicle){
            return $this->json->error('Company vehicle was not found in the system. Please report if this persists');
        }

        // save
        DB::beginTransaction();
        try{
            // Save the visit info
            $activity = $this->saveActivity($vehicle, $validator->validated());

            if(!$activity || is_string($activity)){
                DB::rollback();
                return $this->json->error('Unable to check in the vehicle. Please use other check in method and report if this persists');
            }

            DB::commit();
            return $this->json->success('The vehicle '.$vehicle->registration_no.' has been checked in successfully at '.$activity->fmt_time);

        }catch(Exception $e){
            DB::rollback();
            return $this->json->error($e->getMessage().'Unable to capture all data. Please report if this persists', ['exception'=>$e->getMessage()]);
        }
    }

    function saveActivity($vehicle, $data){
        try{
            $activity = new Activity([
                'user_id' => auth('sanctum')->id(),
                'by_id' => $vehicle->id,
                'by_type' => Activity::BY_COMPANY_VEHICLE, // A vehicle is being checked in
                'site_id' => auth('sanctum')->user()->site_id,
                'vehicle_id' => null, // This is only reserved for staff and visitor owned vehicle ids
                'type' => Activity::TYPE_CHECK_IN
            ]);

            if(!$activity->save()) return false;

            $driver_activity = new DriverActivity([
                'activity_id' => $activity->id,
                'driver_id' => $data['driver_id'],
                'task' => $data['task'] ?? 'Unspecified',
                'mileage' => $data['mileage'] ?? 0,
            ]);

            if(!$driver_activity->save()) return false;

            return $activity;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    function userCanCheckInVisitor($data){
        // Staff must be on same site with app user
        $staff = Staff::where('id', $data['staff_id'])
            ->whereHas('company', function($q1){
                $q1->where('site_id', auth('sanctum')->user()->site_id);
            })
            ->first();

        // true means staff is in a company on site where user is authorized
        return $staff != null;
    }
}
