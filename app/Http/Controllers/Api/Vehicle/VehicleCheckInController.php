<?php

namespace App\Http\Controllers\Api\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Drive;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleCheckInController extends Controller
{
    function __invoke(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'driver' => 'required|exists:drivers,id',
            'car' => 'required|exists:vehicles,registration_no',
            'mileage' => 'required|numeric',
            'fuel' => 'required|numeric',
        ]);

        if($validator->fails()) return $this->json->error($validator->errors()->first(), $validator->errors()->all());

        // see if vehicle was checked out
        $drive = Drive::latest('time_out')
            ->whereHas('vehicle', function($q) use($request){
                $q->where('registration_no', $request->post('car'));
            })
            ->where('time_in', null)
            ->first();

        if($drive == null) return $this->json->error('Vehicle has not been checked out from the app today. Please check in manually for now');

        // todo check if vehicle has been checked in?

        $drive->driver_in_id = $request->post('driver');
        $drive->mileage_in = $request->post('mileage');
        $drive->fuel_in = $request->post('fuel');
        $drive->time_in = Carbon::now()->toDateTimeString();

        // user
        $drive->checked_in_by = auth('sanctum')->id();

        try{
            if($drive->save()) return $this->json->success('Done. The vehicle check in details have been saved');

            return $this->json->success('Something went wrong. Please try again or check in manually and contact IT');
        }catch(Exception $e){
            return $this->json->success('Something went wrong. Please try again or check in manually and contact IT');
        }
    }
}
