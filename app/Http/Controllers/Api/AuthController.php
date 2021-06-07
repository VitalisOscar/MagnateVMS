<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    function login(Request $request){
        if(validator()->make($request->post(), [
            'username' => ['required'],
            'password' => ['required'],
            'site_id' => ['required', 'exists:sites,id'],
        ],[
            'site_id.required' => 'Please select a site',
            'site_id.exists' => 'Select a site from the given sites',
        ])->fails()){
            return $this->json->error('Please provide your staff username and password and the site you are logging in from');
        }

        // Login tracking
        $login = new Login([
            'type' => Login::TYPE_USER,
            'credential' => $request->get('username'),
            'site_id' => $request->get('site_id'),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        /** @var User */
        $user = User::where('username', $request->get('username'))->first();

        $login->identifier = $user->id;

        if($user == null){
            $login->status = Login::STATUS_INVALID_CREDENTIAL;
            try{ $login->save(); }catch(Exception $e){};

            return $this->json->error('Incorrect username. Please check and retry');
        }

        // check password
        if(!Hash::check($request->post('password'), $user->getAuthPassword())){
            $login->status = Login::STATUS_INVALID_PASSWORD;
            try{ $login->save(); }catch(Exception $e){};

            return $this->json->error('Incorrect password. Please try again, or contact admin if you forgot');
        }

        $login->status = Login::STATUS_SUCCESS;
        try{ $login->save(); }catch(Exception $e){
            // Saving login has to be done to capture the site id where user is tracking data from
            return $this->json->error('Something went wrong. Please try again');
        };

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
