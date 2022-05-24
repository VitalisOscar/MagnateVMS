<?php

namespace App\Http\Controllers\Data\Drivers;

use App\Helpers\ApiResultSet;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Site;
use App\Models\Vehicle;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;

class SingleDriverController extends Controller
{
    function get(ApiService $api, $driver_id){

        $response = $api->get(ApiService::ROUTE_GET_SINGLE_DRIVER, ['driver_id' => $driver_id]);

        if(!$response->wasSuccessful()){
            return redirect()->route('admin.vehicles.drivers')->withErrors(['status' => $response->message]);
        }

        $data = $response->data;
        $driver = new Driver($data['driver']);
        $driver->created_at = $data['timestamp'] ?? null;


        // $result = new ApiResultSet($response->getResult(), function($data){
        //     return new Driver($data);
        // });

        return response()->view('admin.drivers.single', [
            'driver' => $driver
        ]);
    }

    function update(Request $request, ApiService $api, $driver_id){
        $validator = validator($request->post(), [
            'name' => 'required',
            'department' => 'required',
            'phone' => 'required|regex:/0([0-9]){9}/',
        ],[
            'phone.regex' => 'Provide a valid phone number'
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        $response = $api->post(ApiService::ROUTE_UPDATE_DRIVER, ['driver_id' => $driver_id], [], [
            'name' => $request->post('name'),
            'department' => $request->post('department'),
            'phone' => $request->post('phone'),
        ]);

        if($response->wasSuccessful()){
            return back()
                ->with(['status' => 'Driver details have been updated']);
        }

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
