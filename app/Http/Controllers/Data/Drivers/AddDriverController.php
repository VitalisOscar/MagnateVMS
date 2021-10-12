<?php

namespace App\Http\Controllers\Data\Drivers;

use App\Http\Controllers\Controller;
use App\Imports\DriversImport;
use App\Models\Driver;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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


    function import(Request $request){

        $validator = validator($request->file(), [
            'import' => 'required|file',
        ],[
            'import.required' => 'Please upload a file with the drivers to import',
            'import.file' => 'Please upload a file with the drivers to import'
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator->errors());
        }

        try{
            Excel::import(new DriversImport(), $request->file('import'));

            return redirect()->route('admin.vehicles.drivers')->with([
                'status' => 'Drivers will be imported from the uploaded file'
            ]);
        }catch(Exception $e){
            return back()->withInput()->withErrors(['status' => $e->getMessage().'Oops. Something went wrong']);
        }

    }
}
