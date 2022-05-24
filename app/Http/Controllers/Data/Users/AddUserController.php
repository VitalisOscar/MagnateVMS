<?php

namespace App\Http\Controllers\Data\Users;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;

class AddUserController extends Controller
{
    function __invoke(Request $request, ApiService $api){
        $validator = validator($request->post(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        $response = $api->post(ApiService::ROUTE_ADD_USER, [], [], [
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'password' => $request->post('password'),
        ]);

        if($response->wasSuccessful()){
            return back()
                ->with(['status' => 'User account has been created']);
        }

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
