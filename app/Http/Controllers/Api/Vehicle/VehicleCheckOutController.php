<?php

namespace App\Http\Controllers\Api\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Drive;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleCheckOutController extends Controller
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

        // see if vehicle has already checked out
        $drive = Drive::latest('time_out')
            ->whereHas('vehicle', function($q) use($request){
                $q->where('registration_no', $request->post('car'));
            }) // selected vehicle
            ->where('time_out', '<>', null) // checked out
            ->where('time_in', null) // and not checked back in
            ->whereDate('time_out', Carbon::today()->format('Y-m-d')) // today
            ->first();

        if($drive != null) return $this->json->error('Seems like the vehicle, '.$request->post('car').' has already been checked out from the app today');

        $drive->driver_out_id = $request->post('driver');
        $drive->mileage_out = $request->post('mileage');
        $drive->fuel_out = $request->post('fuel');
        $drive->time_out = Carbon::now()->toDateTimeString();

        // user
        $drive->checked_out_by = auth('sanctum')->id();

        try{
            if($drive->save()) return $this->json->success('Done. The vehicle check out details have been saved');

            return $this->json->success('Something went wrong. Please try again or check out the vehicle manually and contact IT');
        }catch(Exception $e){
            return $this->json->success('Something went wrong. Please try again or check out the vehicle manually and contact IT');
        }
    }
}
