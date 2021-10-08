<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\Site;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
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

        $site = Site::whereId($request->get('site_id'))->first();

        // Login tracking
        $login = new Login([
            'user_type' => 'user',
            'credential' => $request->get('username'),
            'site_id' => $site->id,
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        /** @var User */
        $user = User::where('username', $request->get('username'))->first();

        if($user == null){
            return $this->json->error('There is no user who matches the provided username');
        }

        $login->user_id = $user->id;

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
        $user->site_name = $site->name;

        return $this->json->data($user);
    }

    function reLogin(Request $request){
        if(validator()->make($request->post(), [
            'password' => ['required'],
        ])->fails()){
            return $this->json->error('Please provide your password to authenticate yourself');
        }

        $user = auth('sanctum')->user();

        if($user == null){
            return $this->json->error('Unable to log you in. Please logout the app and do a fresh login');
        }

        // check password
        if(!Hash::check($request->post('password'), $user->getAuthPassword())){
            return $this->json->error('Incorrect password. Please try again, or contact admin if you forgot');
        }

        return $this->json->data($user, "You have logged in successfully");
    }

    function changePassword(Request $request){
        if(validator()->make($request->post(), [
            'current_password' => 'required',
            'new_password' => 'required',
        ])->fails()){
            return $this->json->error('Please provide your current and new password');
        }

        $user = auth('sanctum')->user();

        // check password
        if(!Hash::check($request->post('current_password'), $user->getAuthPassword())){
            return $this->json->error('Incorrect password. Please try again, or contact admin if you forgot');
        }

        $user->password = Hash::make($request->post('new_password'));

        try{
            if($user->save()){
                return $this->json->success('Your password has been updated');
            }
        }catch(Exception $e){};

        return $this->json->error('Something went wrong. Please try again');
    }
}
