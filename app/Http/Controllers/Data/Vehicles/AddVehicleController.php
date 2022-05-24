<?php

namespace App\Http\Controllers\Data\Vehicles;

use App\Http\Controllers\Controller;
use App\Imports\VehiclesImport;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\ApiService;
use App\Services\VehicleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class AddVehicleController extends Controller
{
    function __invoke(Request $request, ApiService $api){
        $validator = validator($request->post(), [
            'description' => 'required',
            'registration_no' => 'required',
        ],[
            'registration_no.unique' => 'A vehicle with the provided registration number has already been added'
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        try{
            $response = $api->post(ApiService::ROUTE_ADD_VEHICLE, [], [], [
                'registration_no' => strtoupper(preg_replace('/ +/', ' ', $request->post('registration_no'))),
                'description' => $request->post('description'),
            ]);

            if($response->wasSuccessful()){
                return back()
                    ->with(['status' => 'Vehicle has been added to the system']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }

    function import(Request $request){

        $validator = validator($request->file(), [
            'import' => 'required|file',
        ],[
            'import.required' => 'Please upload a file with the vehicles to import',
            'import.file' => 'Please upload a file with the vehicles to import'
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator->errors());
        }

        try{
            Excel::import(new VehiclesImport(), $request->file('import'));

            return redirect()->route('admin.vehicles')->with([
                'status' => 'Vehicles will be imported from the uploaded file'
            ]);
        }catch(Exception $e){
            return back()->withInput()->withErrors(['status' => $e->getMessage().'Oops. Something went wrong']);
        }

    }
}
