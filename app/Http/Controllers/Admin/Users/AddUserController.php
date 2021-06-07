<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddUserController extends Controller
{
    function __invoke(Request $request){
        $validator = validator($request->post(), [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required',
        ],[
            'username.unique' => 'This username belongs to another account'
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        $user = new User([
            'username' => $request->post('username'),
            'name' => $request->post('name'),
            'password' => Hash::make($request->post('password')),
            'site_id' => 1, // TODO remove this
        ]);

        try{
            if($user->save()){
                return back()
                    ->with(['status' => 'User account has been created']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
