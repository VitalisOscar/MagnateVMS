<?php

namespace App\Http\Controllers\Admin\Drivers;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;

class SingleDriverController extends Controller
{
    function get($driver_id){
        $driver = Vehicle::whereId($driver_id)
            ->first();

        if($driver == null){
            return redirect()->route('admin.vehicles.drivers');
        }

        return response()->view('admin.drivers.single', [
            'driver' => $driver
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
