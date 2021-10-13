<?php

namespace App\Http\Controllers\CheckOut;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Staff;
use App\Models\Vehicle;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VisitorCheckOutController extends Controller
{
    function __invoke(Request $request)
    {
        $rules = [
            'items' => 'nullable|string',
            'car_registration' => 'nullable',
            'id_number' => 'required|regex:/([0-9]){7,8}/',
        ];

        $validator = Validator::make(array_merge($request->post(), $request->file()), $rules);

        if($validator->fails()){
            return $this->json->errors($validator->errors()->all());
        }

        // Get visitor
        $visitor = $this->getVisitor($request->get('id_number'));

        if(!$visitor){
            return $this->json->error('The visitor has not checked in through the app today. Please check out using other method');
        }

        // Has visitor checked in?
        $latest_check_in = $this->getLatestCheckIn($visitor);

        if(!$this->everCheckedIn($visitor, $latest_check_in)){
            DB::rollback();
            return $this->json->error($visitor->first_name.' has not checked in through the app today. Please check out using other method');
        }

        // Has visitor already checked out
        $latest_check_out = $this->getLatestCheckOut($visitor);

        if(!$this->stillCheckedIn($visitor, $latest_check_in, $latest_check_out)){
            DB::rollback();
            return $this->json->error($visitor->first_name.' was checked out through the app at '.$latest_check_out->fmt_time);
        }


        // Attempt
        DB::beginTransaction();
        try{

            // Save the activity
            $activity = $this->saveActivity($visitor, $validator->validated(), $latest_check_in);

            if(!$activity || is_string($activity)){
                DB::rollback();
                return $this->json->error('Unable to check out '.$visitor->first_name.'. Please report if this persists');
            }

            DB::commit();
            return $this->json->success($visitor->first_name.' has been checked out successfully at '.$activity->fmt_time);

        }catch(Exception $e){
            DB::rollback();
            return $this->json->error($e->getMessage().'Unable to capture all data. Please report if this persists', ['exception'=>$e->getMessage()]);
        }
    }

    function getLatestCheckIn($visitor){
        // Gets the last check in activity, happened today
        return $visitor->check_ins()
            ->onDate(Carbon::today())
            ->latest('time')
            ->first();
    }

    function getLatestCheckOut($visitor){
        // Gets the last check in activity, happened today
        return $visitor->check_outs()
        ->onDate(Carbon::today())
        ->latest('time')
        ->first();
    }

    function everCheckedIn($visitor, $latest_check_in){
        return $latest_check_in != null;
    }

    function stillCheckedIn($visitor, $latest_check_in, $latest_check_out){
        if(!$latest_check_out) return true;

        // If after
        return $latest_check_in->time->isAfter($latest_check_out->time);
    }

    function saveActivity($visitor, $data, $latest_check_in){
        try{
            $vehicle = null;

            if(isset($data['car_registration'])){
                $vehicle = $this->getVehicle($visitor, $data['car_registration']);

                if(!$vehicle || is_string($vehicle)) return false;
            }

            $checkout = new Activity([
                'user_id' => auth('sanctum')->id(),
                'by_id' => $visitor->id,
                'by_type' => Activity::BY_VISITOR,
                'site_id' => auth('sanctum')->user()->site_id,
                'vehicle_id' => $vehicle ? $vehicle->id : null,
                'type' => Activity::TYPE_CHECK_OUT,
                'checkin_activity_id' => $latest_check_in->id
            ]);

            if(!$checkout->save()) return false;

            // Duplicate data of the last visit
            $check_out_visit = new Visit([
                'activity_id' => $checkout->id,
                'reason' => $latest_check_in->visit->reason,
                'company_id' => $latest_check_in->visit->company_id,
                'staff_id' => $latest_check_in->visit->staff_id,
                'host' => $latest_check_in->visit->host,
                'from' => $visitor->from,
                'card_number' => $latest_check_in->visit->card_number,
                'items' => $data['items'] ?? null,
                'signature' => $latest_check_in->visit->signature,
                'check_in_visit_id' =>$latest_check_in->visit->id
            ]);

            $latest_check_in->checkout_activity_id = $checkout->id;

            if(!($check_out_visit->save() && $latest_check_in->save())) return false;

            return $checkout;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    function getVehicle($visitor, $registration_no){

        try{
            $vehicle = $visitor->vehicles()
                ->regNo($registration_no)
                ->first();

            if($vehicle != null){
                return $vehicle;
            }

            $vehicle = new Vehicle([
                'registration_no' => preg_replace('/ +/', ' ', $registration_no),
                'description' => "Visitor's vehicle",
                'owner_id' => $visitor->id,
                'owner_type' => Vehicle::OWNER_VISITOR,
            ]);

            if($vehicle->save()){
                return $vehicle;
            }

            return false;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    function getVisitor($id_number){
        return Visitor::where('id_number', $id_number)->first();
    }

    function createVisitor($request){
        $visitor = new Visitor([
            'name' => $request->post('name'),
            'from' => $request->post('from'),
            'phone' => $request->post('phone'),
            'id_number' => $request->post('id_number'),
            'id_photo' => $request->post('id_photo'),
        ]);

        try{
            if($visitor->save()){
                return $visitor;
            }
        }catch(Exception $e){
            return $e->getMessage();
        }

        return false;
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
