<?php

namespace App\Http\Controllers\Data\Drivers;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Site;
use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;

class SingleDriverController extends Controller
{
    function get($driver_id){
        $driver = Driver::whereId($driver_id)
            ->first();

        if($driver == null){
            return redirect()->route('admin.vehicles.drivers');
        }

        return response()->view('admin.drivers.single', [
            'driver' => $driver
        ]);
    }

    function update(Request $request, $driver_id){
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

        $driver = Driver::whereId($driver_id)
            ->first();

        if($driver == null){
            return redirect()->route('admin.vehicles.drivers');
        }

        $driver->name = $request->post('name');
        $driver->phone = $request->post('phone');
        $driver->department = $request->post('department');

        try{
            if($driver->save()){
                return back()->with(['status' => 'Driver details have been updated']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
