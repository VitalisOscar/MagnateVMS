<?php

namespace App\Http\Controllers\Api\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Staff;
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
            'staff_id' => 'required|exists:staff,id',
            'car_registration' => 'nullable',
            'card_number' => 'nullable',
            'signature' => 'required',
            'items_in' => 'nullable|string',
            'id_number' => 'required|regex:/([0-9]){7,8}/',
        ];

        $existing_visitor = true;
        $visitor = Visitor::where('id_number', $request->get('id_number'))->first();

        if($visitor == null){
            // new visitor
            $rules = array_merge($rules, [
                'name' => 'required|string',
                'phone' => 'required|regex:/0([0-9]){9}/',
		        'from' => 'required|string',
                'id_photo' => 'required', // should be image
            ]);

            $existing_visitor = false;
        }

        $validator = Validator::make(array_merge($request->post(), $request->file()), $rules);

        if($validator->fails()){
            return $this->json->errors($validator->errors()->all());
        }

        // save
        DB::beginTransaction();
        try{
            if($existing_visitor){
                // exisiting
                // can be checked in?
                if(!$visitor->canCheckIn()){
                    return $this->json->error('This visitor is already checked in the site, and has not checked out today through the app');
                }

            }else{
                // Handle new visitor
                // Save visitor record first
                if(!$visitor->save()){
                    DB::rollback();
                    return $this->json->error('Unable to capture all data. Please report if this persists');
                }
            }

            // Save the visit info
            $visit = $this->saveVisit($visitor, $validator->validated());

            if(!$visit){
                DB::rollback();
                return $this->json->error('Unable to capture all data. Please report if this persists');
            }

            DB::commit();
            return $this->json->success('Visitor has been checked in and can proceed');
        }catch(Exception $e){
            DB::rollback();
            return $this->json->error('Unable to capture all data. Please report if this persists', ['exception'=>$e->getMessage()]);
        }
    }

    function saveVisit($visitor, $data){
        /** @var VehicleService */
        $vehicleService = resolve(VehicleService::class);

        $vehicle_id = null;
        if(isset($data['car_registration'])){
            $vehicle = $vehicleService->getOrAddVehicle($data['car_registration'], 'visitor', $visitor->id);

            if($vehicle == null) return false;
            $vehicle_id = $vehicle->id;
        }

        $visit = new Visit([
            'checked_in_by' => auth('sanctum')->id(),
            'visitor_id' => $visitor->id,
            'reason' => $data['reason'],
            'staff_id' => $data['staff_id'],
	        'site_id' => auth('sanctum')->user()->site_id,
            'vehicle_id' => $vehicle_id,
            'from' => $visitor->from,
            'card_number' => isset($data['card_number']) ? $data['card_number']:null,
            'items_in' => isset($data['items_in']) ? $data['items_in']:null,
            'signature' => $data['signature'],
        ]);

        if($visit->save()) return $visit;

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
