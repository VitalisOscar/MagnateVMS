<?php

namespace App\Http\Controllers\Data\Sites;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Exception;
use Illuminate\Http\Request;

class SingleSiteController extends Controller
{
    function get($site_id){
        $site = Site::whereId($site_id)
            ->withCount('companies')
            ->with('companies')
            ->first();

        if($site == null){
            return redirect()->route('admin.sites')
                ->withErrors([
                    'status' => 'Site does not exist, might have been deleted or url is incorrect'
                ]);
        }

        return response()->view('admin.sites.single', [
            'site' => $site
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
