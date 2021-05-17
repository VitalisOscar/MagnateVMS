<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Visit;
use App\Models\Visitor;
use App\Repository\VisitorRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{

    function basic(Request $request){
        // $s = DB::select();

	$visitors_out = Visit::today()->atSite()->gone()->count();
	$visitors_in = Visit::today()->stillIn()->atSite()->count();
	$staff = 10;

        return $this->json->data(['visitors' => [
	    ['name'=>'John Doe', 'time'=>'08:44', 'about'=>'Visitor', 'activity'=>'check_in', 'type'=>'visitor', 'data' => ''],
		['name'=>'Calvin Harris', 'time'=>'08:50', 'about'=>'Visitor', 'activity'=>'check_in', 'type'=>'visitor', 'data' => ''],
		['name'=>'Bob Stuart', 'time'=>'08:53', 'about'=>'Visitor', 'activity'=>'check_out', 'type'=>'visitor', 'data' => ''],
		['name'=>'Jane Atieno', 'time'=>'09:00', 'about'=>'Visitor', 'activity'=>'check_out', 'type'=>'visitor', 'data' => ''],
		['name'=>'Allan Munyika', 'time'=>'09:05', 'about'=>'Visitor', 'activity'=>'check_in', 'type'=>'visitor', 'data' => ''],
	], 'summary' => [
		'checked_in' => $visitors_in,
		'checked_out' => $visitors_out,
		'staff' => $staff
	    ]
    	]);

    }

}
