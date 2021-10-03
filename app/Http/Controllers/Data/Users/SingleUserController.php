<?php

namespace App\Http\Controllers\Data\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SingleUserController extends Controller
{
    function get($username){
        $user = User::whereUsername($username)->first();

        if($user == null){
            return redirect()->route('admin.users');
        }

        return response()->view('admin.users.single', [
            'user' => $user,
            'recent_logins' => $user->logins()->latest('time')->limit(3)->get()
        ]);
    }

    function update(Request $request, $username){
        $user = User::whereUsername($username)->first();

        if($user == null){
            return back()
                ->withInput()
                ->withErrors(['status' => "Unable to find user with username '$username'"]);
        }

        $validator = validator($request->post(), [
            'name' => 'required',
            'username' => 'required|unique:users,username,'.$user->id,
            'password' => 'nullable',
        ],[
            'username.unique' => 'This username belongs to another account'
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        if($user->username == $request->post('username') &&
            $user->name == $request->post('name') &&
            !$request->filled('password')
        ){
            return back()
                ->withInput()
                ->with(['status' => 'No changes have been made to be saved']);
        }

        $user->username = $request->post('username');
        $user->name = $request->post('name');

        if($request->filled('password')){
            $user->password = Hash::make($request->post('password'));
        }

        try{
            if($user->save()){
                return back()
                    ->with(['status' => 'Changes have been saved']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }

    function delete($username){
        $user = User::whereUsername($username)->first();

        if($user == null){
            return back()
                ->withInput()
                ->withErrors(['status' => "Unable to find user with username '$username'"]);
        }

        try{
            if($user->delete()){
                return back()
                    ->withInput()
                    ->with(['status' => 'User account has been deleted from the system']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
