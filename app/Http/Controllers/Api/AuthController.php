<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    function login(Request $request){
        /** @var User */
        $user = User::where('username', $request->get('username'))->first();

        if($user == null){
            return $this->json->error('Incorrect username. Please check and retry');
        }

        // check password
        if(!Hash::check($request->post('password'), $user->getAuthPassword())){
            return $this->json->error('Incorrect password. Please try again, or contact admin if you forgot');
        }

        // create token
        $token = $user->createToken($request->userAgent());

        // add token to user data
        $user->token = $token->plainTextToken;

	// Add site name
	$user->site_name = $user->site->name;

        return $this->json->data($user);
    }

    function changePassword(){

    }
}
