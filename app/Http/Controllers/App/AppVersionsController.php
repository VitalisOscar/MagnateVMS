<?php

namespace App\Http\Controllers\App;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class AppVersionsController extends Controller
{

    function __invoke(Request $request){
        return response()->view('admin.app.versions', [
            'result' => new ResultSet(AppVersion::latest('time'))
        ]);
    }

    function getUpdates(){
        $latest = AppVersion::latest('time')->first();

        return $this->json->data([
            'latest_version' => $latest->version,
            'latest_version_url' => URL::signedRoute('app.latest_url', [], now()->addMinutes(15)),
        ]);
    }

    function downloadLatest(){
        $latest = AppVersion::latest('time')->first();

        return response()->download(storage_path('app/public/'.$latest->url), 'MagnateVMS_v'.$latest->version.'.apk', [
            'Content-Type'=>'application/vnd.android.package-archive'
        ]);
    }

    function add(Request $request){
        if(validator($request->all(), [
            'version' => 'required',
            'apk_file' => 'required|file',
        ])->fails()){
            return back()->withInput()->withErrors([
                'status' => 'Please specify the new app version and select the related .apk file'
            ]);
        }

        try{
            $v = new AppVersion([
                'version' => $request->post('version'),
                'url' => $request->file('apk_file')->store('apks', 'public')
            ]);

            if($v->save()){
                return redirect()->route('admin.app.versions')->with([
                    'status' => 'The new app version has been added successfully'
                ]);
            }
        }catch(Exception $e){
            return back()->withInput()->withErrors(['status' => 'Oops. Something went wrong: '.$e->getMessage()]);
        }

        return back()->withInput()->withErrors(['status' => 'Unable to add new app version']);
    }

}
