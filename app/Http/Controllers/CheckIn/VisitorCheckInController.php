<?php

namespace App\Http\Controllers\CheckIn;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Staff;
use App\Models\Vehicle;
use App\Models\Visit;
use App\Models\Visitor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VisitorCheckInController extends Controller
{
    function __invoke(Request $request)
    {

        // for new and saved visitors
        $rules = [
            'reason' => 'required|string',
            'staff_id' => 'nullable|exists:staff,id',
            'company_id' => 'required|exists:companies,id',
            'host' => 'nullable|string',
            'car_registration' => 'nullable',
            'card_number' => 'nullable',
            'signature' => 'required',
            'items' => 'nullable|string',
            'id_number' => 'required|regex:/([0-9]){7,8}/',
        ];

        $visitor = $this->getVisitor($request->get('id_number'));

        if(!$visitor){
            // new visitor
            $rules = array_merge($rules, [
                'name' => 'required|string',
                'phone' => 'required|regex:/0([0-9]){9}/',
		        'from' => 'required|string',
                'id_photo' => 'required', // should be image
            ]);
        }

        $validator = Validator::make(array_merge($request->post(), $request->file()), $rules);

        if($validator->fails()){
            return $this->json->errors($validator->errors()->all());
        }

        // save
        DB::beginTransaction();
        try{

            if(!$visitor){
                $visitor = $this->createVisitor($request);

                if(!$visitor){
                    DB::rollback();
                    return $this->json->error('Unable to check in the visitor. Please report if this persists');
                }
            }

            // Check if visitor is already checked in
            $last = $visitor->last_activity;

            if($last && $last->isCheckIn() && $last->wasToday() && $last->site_id == auth('sanctum')->user()->site_id){
                return $this->json->error($visitor->first_name.' has already been checked in via the app today at '.$last->fmt_time.', and has not been checked out. We suggest checking the visitor out first and trying again');
            }

            // Save the visit info
            $activity = $this->saveActivity($visitor, $validator->validated());

            if(!$activity || is_string($activity)){
                DB::rollback();
                return $this->json->error('Unable to check in '.$visitor->first_name.'. Please report if this persists');
            }

            DB::commit();
            return $this->json->success($visitor->first_name.' has been checked in successfully at '.$activity->fmt_time);

        }catch(Exception $e){
            DB::rollback();
            return $this->json->error($e->getMessage().'Unable to capture all data. Please report if this persists', ['exception'=>$e->getMessage()]);
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

    function saveActivity($visitor, $data){
        try{
            $vehicle = null;

            if(isset($data['car_registration'])){
                $vehicle = $this->getVehicle($visitor, $data['car_registration']);

                if(!$vehicle || is_string($vehicle)) return false;
            }

            $activity = new Activity([
                'user_id' => auth('sanctum')->id(),
                'by_id' => $visitor->id,
                'by_type' => Activity::BY_VISITOR,
                'site_id' => auth('sanctum')->user()->site_id,
                'vehicle_id' => $vehicle ? $vehicle->id : null,
                'type' => Activity::TYPE_CHECK_IN
            ]);

            if(!$activity->save()) return false;

            $visit = new Visit([
                'activity_id' => $activity->id,
                'reason' => $data['reason'],
                'company_id' => $data['company_id'],
                'staff_id' => $data['staff_id'] ?? null,
                'host' => $data['host'] ?? 'Staff Member',
                'from' => $visitor->from,
                'card_number' => $data['card_number'] ?? null,
                'items' => $data['items'] ?? null,
                'signature' => $data['signature'],
            ]);

            if(!$visit->save()) return false;

            return $activity;
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
