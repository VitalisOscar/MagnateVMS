<?php

namespace App\Http\Controllers\Admin\Sites;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddSiteController extends Controller
{
    function __invoke(Request $request){
        $validator = validator($request->post(), [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        $site = new Site([
            'name' => $request->post('name'),
        ]);

        try{
            if($site->save()){
                return redirect()
                    ->route('admin.sites.single', $site->id)
                    ->with(['status' => 'Site has been saved']);
            }
        }catch(Exception $e){}

        return back()
            ->withInput()
            ->withErrors(['status' => 'Something went wrong. Please try again']);
    }
}
