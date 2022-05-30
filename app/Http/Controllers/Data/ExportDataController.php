<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Login;
use App\Models\Site;
use App\Models\Staff;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportDataController extends Controller
{
    function __invoke(Request $request, $type)
    {
        $type = strtolower($type);
        $export = null;

        $time = today()->toDateTimeString();
        $time = str_replace(['-', ' ', ':'], '_', $time);

        if($type == 'users') $export = $this->users();
        if($type == 'drivers') $export = $this->drivers();
        if($type == 'vehicles') $export = $this->vehicles();
        if($type == 'sites') $export = $this->sites();
        if($type == 'companies') $export = $this->companies();
        if($type == 'staff') $export = $this->staff();
        if($type == 'visitors') $export = $this->visitors();
        if($type == 'logins') $export = $this->logins();
        if($type == 'activities') $export = $this->activities();

        if($export != null){
            $path = "exports/".$type."_".$time.".json";
            Storage::put($path, $export);
            // return response()->file(storage_path('app/'.$path));
            return response()->json(json_decode($export, 1));
        }

        return back()->withErrors([
            'status' => 'Invalid export type: '.$type
        ]);
    }

    function activities(){
        $page = request()->query->get('page');
        $offset = ($page - 1) * 1000;

        // Get all
        $models = Activity::with('checkin_activity', 'checkout_activity', 'user', 'vehicle', 'site', 'by', 'visit', 'driver_task')
            ->limit(1000)
            ->offset($offset)
            ->get();

        foreach($models as $key => $model){
            $this->fmt_user($models[$key]->user);
            $this->fmt_site($models[$key]->site);
            if($models[$key]->vehicle) $this->fmt_vehicle($models[$key]->vehicle);

            if(isset($models[$key]->by->vehicles)){
                unset($models[$key]->by->vehicles);
            }

            if(isset($models[$key]->checkin_activity)){
                $this->fmt_activity($models[$key]->checkin_activity);
            }

            if(isset($models[$key]->checkout_activity)){
                $this->fmt_activity($models[$key]->checkout_activity);
            }

            $this->fmt_activity($models[$key]);
        }

        return json_encode($models);
    }

    function sites(){
        // Get all
        $sites = Site::all();

        foreach($sites as $k => $site){
            $this->fmt_site($sites[$k]);
        }

        return json_encode($sites);
    }

    function companies(){
        // Get all
        $models = Company::with('site')->get();

        foreach($models as $key => $model){
            $this->fmt_company($models[$key]);
            $models[$key]->site = $this->fmt_site($model->site);
        }

        return json_encode($models);
    }

    function staff(){
        // Get all
        $models = Staff::with('company', 'company.site', 'last_activity')->get();

        foreach($models as $key => $model){
            $this->fmt_staff($models[$key]);
        }

        return json_encode($models);
    }

    function users(){
        // Get all
        $models = User::get();

        foreach($models as $key => $model){
            $this->fmt_user($models[$key]);
        }

        return json_encode($models);
    }

    function drivers(){
        // Get all
        $models = Driver::get();

        foreach($models as $key => $model){
            $this->fmt_driver($models[$key]);
        }

        return json_encode($models);
    }

    function vehicles(){
        // Get all
        $models = Vehicle::with('owner')->get();

        foreach($models as $key => $model){
            $this->fmt_vehicle($models[$key]);

            if(isset($models[$key]->owner->vehicles)){
                unset($models[$key]->owner->vehicles);
            }

            if($model->owner_type == Vehicle::OWNER_STAFF){
                $this->fmt_staff($models[$key]->owner);
            }
            // if($model->owner_type == Vehicle::OWNER_VISITOR) $this->fmt_visitor($models[$key]->owner);
        }

        return json_encode($models);
    }

    function logins(){
        // Get all
        $models = Login::with('user', 'site')->get();

        foreach($models as $key => $model){
            $this->fmt_login($models[$key]);
            $this->fmt_site($models[$key]->site);
            $this->fmt_user($models[$key]->user);
        }

        return json_encode($models);
    }

    function visitors(){
        // Get all
        $models = Visitor::with('last_activity', 'first_activity')->get();

        foreach($models as $key => $model){
            $this->fmt_visitor($models[$key]);
        }

        return json_encode($models);
    }

    function fmt_user($model){
        $ca = $model->created_at;
        $ca = str_replace("T", " ", $ca);
        $ca = str_replace("Z", "ff", $ca);
        $model->timestamp = $ca;
        $username = $model->username;
        $model->email = str_replace(" ", "", $username).'@gmail.com';
        $model->searchKey = $this->createSearchKey($model->searchKey, $model->name, $model->email);

        // unset($model->created_at);
        unset($model->updated_at);
        unset($model->date_added);
        // unset($model->username);
    }

    function fmt_site($model){
        if($model == null) return;

        $model->timestamp = $model->added_at;
        // unset($model->added_at);
        unset($model->options);
    }

    function fmt_company($model){
        if($model == null) return;

        $model->timestamp = $model->added_at;
        // unset($model->added_at);
        unset($model->site_id);
    }

    function fmt_activity($model){
        if($model == null) return;

        $model->timestamp = $model->time;
        $model->created_at = $model->time;
        $model->date = $model->fmt_date;
        $model->time = $model->fmt_time;
        // unset($model->created_at);

        if($model->by_type == Activity::BY_VISITOR){
            $by = $model->by;
            $this->fmt_visitor($by ?? null);
            $model->by = $by;
        }

        if($model->by_type == Activity::BY_STAFF){
            $by = $model->by;
            $this->fmt_staff($by ?? null);
            $model->by = $by;
        }

        if($model->by_type == Activity::BY_COMPANY_VEHICLE){
            $by = $model->by;
            $this->fmt_vehicle($by ?? null);
            $model->by = $by;
        }

        $model->searchKey = $this->createSearchKey($model->searchKey, $model->by->name ?? $model->by->registration_no ?? null, $model->vehicle->registration_no ?? null);

        unset($model->site_id);
        unset($model->vehicle_id);
        unset($model->by_id);
        unset($model->user_id);
    }

    function fmt_login($model){
        if($model == null) return;

        $model->timestamp = $model->time;
        // unset($model->time);
        unset($model->site_id);
        unset($model->user_id);
    }

    function fmt_staff($model){
        if($model == null) return;

        $model->timestamp = $model->added_at;

        $company = $model->company;
        $this->fmt_company($company ?? null);
        $company->site = $this->fmt_site($model->company->site ?? null);

        $model->company = $company;

        $model->searchKey = $this->createSearchKey($model->searchKey, $model->name, $model->phone, $model->department);

        // unset($model->added_at);
        unset($model->company_id);
    }

    function fmt_visitor($model){
        if($model == null) return;

        $model->timestamp = $model->first_activity->time ?? now()->toDateTimeString();

        $model->searchKey = $this->createSearchKey($model->searchKey, $model->name, $model->phone, $model->from);
        // unset($model->first_activity);
    }

    function fmt_driver($model){
        if($model == null) return;

        $model->timestamp = $model->added_at;
        $model->searchKey = $this->createSearchKey($model->searchKey, $model->name, $model->phone, $model->department);
        // unset($model->added_at);
    }

    function fmt_vehicle($model){
        if($model == null) return;

        $model->timestamp = now()->toDateTimeString();
        $model->searchKey = $this->createSearchKey($model->searchKey, $model->registration_no);

        unset($model->owner_id);
    }

    function batchProcess($batch_size, $data, $callable){
        $max = count($data);
        $batches = ceil($max / $batch_size);

        for($i = 0; $i<$batches; $i++){
            $start = $i * $batch_size;
            $end = $start + $batch_size;

            if($end > $max){
                $end = $max;
            }

            for($j = $start; $j < $end; $j++){
                return $callable($data[$j]);
            }
        }
    }


    function createSearchKey($key, ...$from){
        if($key == null) $key = [];

        $keywords = [];

        for($i = 0; $i < count($from); $i++){
            // Skip if null
            if($from[$i] == null){
                continue;
            }

            // If it has a space, split it and add each part to keywords
            $parts = explode(" ", $from[$i]);
            for($j = 0; $j < count($parts); $j++){
                array_push($keywords, $parts[$j]);
            }

            // Push the whole word
            array_push($keywords, $from[$i]);
        }

        // Loop through from
        for($i = 0; $i < count($keywords); $i++){
            // Remove spaces
            $keywords[$i] = str_replace(" ", "", $keywords[$i]);

            // To lowercase
            $keywords[$i] = strtolower($keywords[$i]);

            // Loop throgh each character
            $temp = "";
            for($j = 0; $j < strlen($keywords[$i]) + 1; $j++){
                // Concatenate with previous chars to make a key
                $temp = substr($keywords[$i], 0, $j);

                if(!in_array($temp, $key) && $temp != ""){
                    array_push($key, $temp);
                }
            }
        }

        return $key;
    }
}
