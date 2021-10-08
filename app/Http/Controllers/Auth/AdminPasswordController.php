<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminPasswordController extends Controller
{
    function change(Request $request){
        $validator = Validator::make($request->post(), [
            'password' => ['required'],
            'new_password' => ['required'],
            'confirm_password' => ['required', 'same:new_password']
        ],[
            'confirm_password.same' => 'Passwords do not match!'
        ]);

        if($validator->fails()){
            $validator->errors()->add('status', 'Please correct the highlighted errors on the form');
            return back()->withInput()->withErrors($validator->errors());
        }

        // Reauthentication
        $admin = Auth::guard('admin')->user();
        if(!Hash::check($request->post('password'), $admin->getAuthPassword())){
            return back()->withInput()->withErrors(['status'=>'Unable to authenticate you using the password provided']);
        }

        // Change password
        $admin->password = Hash::make($request->post('new_password'));

        // save
        if($admin->save()){
            return back()->with(['status' => 'Your new password has been set successfully']);
        }

        return back()->withInput()->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
