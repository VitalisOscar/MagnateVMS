<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    function getAll(){
        $staff = Staff::all()->each(function($s){
            $time = Carbon::createFromTimeString($s->created_at);

            if($time->isToday()){
                $s->date = "Today";
            }else if($time->isYesterday()){
                $s->date = "Yesterday";
            }else{
                $s->date = substr($time->monthName, 0, 3)." ".($time->day < 10 ? '0':'').$time->day.", ".$time->year;
            }
        });

        return view('admin.staff.list', ['staff' => $staff]);
    }

    function add(Request $request){
        $validator = Validator::make($request->post(), [
            'name' => ['required'],
            'username' => ['required', 'unique:staff,username'],
            'password' => ['required'],
            'role' => ['required', 'in:'.Staff::ROLE_ADMIN.','.Staff::ROLE_STAFF]
        ], [
            'username.unique' => 'The username is already associated with another account',
            'role.in' => 'The specified role is invalid'
        ]);

        if($validator->fails()){
            $validator->errors()->add('status', 'Please correct the highlighted errors on the form');
            return back()->withInput()->withErrors($validator->errors());
        }

        $staff = new Staff([
            'name' => $request->post('name'),
            'username' => $request->post('username'),
            'password' => Hash::make($request->post('password')),
            'role' => $request->post('role')
        ]);

        DB::beginTransaction();

        // Staff log
        $admin = auth('staff')->user();

        if($staff->save()){
            $log = new StaffLog([
                'staff_id' => $admin->id,
                'activity' => "Created new staff account for '".$staff->name."'",
                'item' => StaffLog::ITEM_STAFF,
                'item_id' => $staff->id
            ]);

            if($log->save()){
                DB::commit();
                return redirect()->route('admin.staff')
                    ->with(['status' => 'Staff account for '.$staff->name.' has been added to the system']);
            }
        }

        DB::rollback();
        return back()->withInput()->withErrors([
            'status' => 'Unable to create account. Please try again'
        ]);
    }
}
