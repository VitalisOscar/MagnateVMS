<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddVehicleController extends Controller
{
    function __invoke(Request $request, VehicleService $vehicleService){
        $validator = validator($request->post(), [
            'description' => 'required',
            'registration_no' => 'required|unique:vehicles',
        ],[
            'registration_no.unique' => 'A vehicle with the provided registration number has already been added'
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        try{
            $vehicle = $vehicleService->addCompanyVehicle($request->post('registration_no'), $request->post('description'));

            if($vehicle){
                return back()
                    ->with(['status' => 'Vehicle has been added to the system']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
