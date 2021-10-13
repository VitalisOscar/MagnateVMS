<?php

namespace App\Http\Controllers\CheckIn;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Staff;
use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffCheckInController extends Controller
{
    function __invoke(Request $request)
    {
        $validator = validator($request->post(), [
            'staff_id' => 'required|exists:staff,id',
            'car_registration' => 'nullable'
        ], [
            'staff_id.required' => 'Please use the provided check in interface on the app to check in staff members',
            'staff_id.exists' => 'Please use the provided check in interface on the app to check in staff members',
        ]);

        if($validator->fails()){
            return $this->json->errors($validator->errors()->all());
        }

        // save
        DB::beginTransaction();
        try{
            // Get the staff
            $staff = Staff::where('id', $request->post('staff_id'))->first();

            // See if checked in
            $last = $staff->last_activity;

            if($last && $last->isCheckIn() && $last->wasToday() && $last->site_id == auth('sanctum')->user()->site_id){
                return $this->json->error($staff->first_name.' has not been checked out via the app since the last check in');
            }

            // Save the activity
            $activity = $this->saveActivity($staff, $validator->validated());

            if(!$activity || is_string($activity)){
                DB::rollback();
                return $this->json->error('Unable to check in '.$staff->first_name.'. Please report if this persists');
            }

            DB::commit();
            return $this->json->success($staff->first_name.' has been checked in successfully at '.$activity->fmt_time);

        }catch(Exception $e){
            DB::rollback();
            return $this->json->error($e->getMessage().'Unable to capture all data. Please report if this persists', ['exception'=>$e->getMessage()]);
        }
    }

    function saveActivity($staff, $data){
        try{
            $vehicle = null;

            if(isset($data['car_registration'])){
                $vehicle = $this->getVehicle($staff, $data['car_registration']);

                if(!$vehicle || is_string($vehicle)) return false;
            }

            $activity = new Activity([
                'user_id' => auth('sanctum')->id(),
                'by_id' => $staff->id,
                'by_type' => Activity::BY_STAFF,
                'site_id' => auth('sanctum')->user()->site_id,
                'vehicle_id' => $vehicle ? $vehicle->id : null,
                'type' => Activity::TYPE_CHECK_IN
            ]);

            if(!$activity->save()) return false;

            return $activity;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    function getVehicle($staff, $registration_no){

        try{
            $vehicle = $staff->vehicles()
                ->regNo($registration_no)
                ->first();

            if($vehicle != null){
                return $vehicle;
            }

            $vehicle = new Vehicle([
                'registration_no' => preg_replace('/ +/', ' ', $registration_no),
                'description' => "Staff vehicle",
                'owner_id' => $staff->id,
                'owner_type' => Vehicle::OWNER_STAFF,
            ]);

            if($vehicle->save()){
                return $vehicle;
            }

            return false;
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
