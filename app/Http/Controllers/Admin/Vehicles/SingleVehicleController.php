<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;

class SingleVehicleController extends Controller
{
    function get($vehicle_id){
        $vehicle = Vehicle::whereId($vehicle_id)
            ->first();

        if($vehicle == null){
            return redirect()->route('admin.vehicles');
        }

        return response()->view('admin.vehicles.single', [
            'vehicle' => $vehicle
        ]);
    }

    function update(Request $request, $site_id){
        $v = validator($request->post(), [
            'name' => 'required|string'
        ], [
            'name.required' => 'Please enter the site name',
            'name.string' => 'Please enter a valid site name',
        ]);

        if($v->fails()){
            return back()->withInput()->withErrors($v->errors());
        }

        $site = Site::whereId($site_id)
            ->first();

        if($site == null){
            return redirect()->route('admin.sites');
        }

        $options = [
            'logins' => $request->boolean('logins'),
        ];

        foreach(Site::TRACKABLES as $key => $trackable){
            $options['tracking'][$key] = $request->boolean('track_'.$key);
        }

        $site->options = $options;
        $site->name = $request->post('name');

        try{
            if($site->save()){
                return back()->with(['status' => 'Site settings for '.$site->name.' have been updated', 'to' => 'options']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
