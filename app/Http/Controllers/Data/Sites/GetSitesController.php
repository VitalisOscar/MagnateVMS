<?php

namespace App\Http\Controllers\Data\Sites;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

class GetSitesController extends Controller
{
    function __invoke(Request $request){
        $q = Site::query()
            ->withCount(['companies', 'staff']);

        $data = new ResultSet($q);

        return $request->is('api*') ?
            $this->json->mixed($data, $data->items) :
            response()->view('admin.sites.all',[
                'result' => $data
            ]);
    }
}
