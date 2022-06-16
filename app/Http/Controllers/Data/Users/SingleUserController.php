<?php

namespace App\Http\Controllers\Data\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SingleUserController extends Controller
{
    function get(ApiService $api, $user_id){
        $response = $api->get(ApiService::ROUTE_GET_SINGLE_USER, [
            'user_id' => $user_id
        ]);

        if(!$response->WasSuccessful()){
            return redirect()->route('admin.users')->withErrors([
                'status' => $response->message
            ]);
        }

        $user = new User($response->data['user']);

        return response()->view('admin.users.single', [
            'user' => $user,
            'recent_logins' => []
        ]);
    }

    function update(Request $request, ApiService $api, $user_id){
        $validator = validator($request->post(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'nullable',
        ],[
            'username.unique' => 'This username belongs to another account'
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        $response = $api->post(ApiService::ROUTE_UPDATE_USER, [
            'user_id' => $user_id
        ], [], [
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'password' => $request->post('password'),
        ]);

        if(!$response->WasSuccessful()){
            return back()
                ->withInput()
                ->withErrors(['status' => $response->message]);
        }

        return back()
            ->with(['status' => 'Changes have been saved']);
    }

    function delete(Request $request, ApiService $api, $user_id){
        $response = $api->post(ApiService::ROUTE_DELETE_USER, [
            'user_id' => $user_id
        ], [], []);

        if(!$response->WasSuccessful()){
            return redirect()->route('admin.users')->withErrors([
                'status' => $response->message
            ]);
        }

        return redirect()->route('admin.users')->with([
            'status' => 'User account has been deleted'
        ]);
    }
}
