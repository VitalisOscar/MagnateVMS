<?php

namespace App\Http\Controllers\Admin\Sites;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetSitesController extends Controller
{
    function __invoke(Request $request){
        $q = Site::query()
            ->withCount(['companies', 'staff']);

        return response()->view('admin.sites.all',[
            'result' => new ResultSet($q, 15)
        ]);
    }
}
