<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    function __invoke(Request $request){
        if(validator()->make($request->post(), [
            'username' => ['required'],
            'password' => ['required']
        ])->fails()){
            return back()->withInput()->withErrors(['status' => 'Please provide your staff username and password']);
        }

        if(auth('admin')->attempt($request->only(['username', 'password']))){
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withInput()->withErrors(['status' => 'Unable to log you in. Please check the provided credentials and try again']);
    }
}
