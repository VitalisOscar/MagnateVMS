<?php

namespace App\Services\Traits;

use App\Models\Site;
use App\Models\Staff;
use App\Models\Visit;
use App\Models\Visitor;
use App\Services\VehicleService;
use Exception;
use Illuminate\Support\Facades\DB;

trait CapturesVisitors{

    function handleExistingVisitor($visitor, $data){
        return $this->handleVisit($visitor, $data);
    }

    function handleNewVisitor($data){
        $visitor = new Visitor([
            'name' => $data['name'],
            'id_number' => $data['id_number'],
            'phone' => $data['phone'],
            'from' => $data['from'],
            'id_photo' => $data['id_photo'],
        ]);

        return $this->handleVisit($visitor, $data);
    }

    function handleVisit($visitor, $data){
        //if(!$this->userCanCheckInVisitor($data)){
            //$site = Site::where('id', auth('sanctum')->user()->site_id)->first();
          //  return 'You can only check in visitors at the site '.$site->name.' The staff members to be seen must also be within that site. Please reach out to admin if the staff is in your site';
        //}

        DB::beginTransaction();
        try{
            // save visit
            // is visitor new?
            if($visitor->id == null){
                if(!$visitor->save()){
                    DB::rollback();
                    return 'Unable to capture all data. Please report if this persists';
                }
            }

            $visit = $this->saveVisit($visitor, $data);
            if(!$visit){
                DB::rollback();
                return 'Unable to capture all data. Please report if this persists';
            }

            DB::commit();
            return true;

        }catch(Exception $e){
            DB::rollback();
            return 'Something went wrong. Please report if this persists';
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
