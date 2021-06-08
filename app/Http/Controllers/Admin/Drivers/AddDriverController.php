<?php

namespace App\Http\Controllers\Admin\Drivers;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Exception;
use Illuminate\Http\Request;

class AddDriverController extends Controller
{
    function __invoke(Request $request){
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

        $driver = new Driver([
            'name' => $request->post('name'),
            'department' => $request->post('department'),
            'phone' => $request->post('phone'),
        ]);

        try{
            if($driver->save()){
                return back()
                    ->with(['status' => 'Driver has been added to the system']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
