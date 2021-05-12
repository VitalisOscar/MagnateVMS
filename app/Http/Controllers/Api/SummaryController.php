<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Repository\VisitorRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{

    function basic(Request $request){
        // $s = DB::select();
        return $this->json->data(['visitors' => [
	    ['name'=>'John Doe', 'time'=>'08:44', 'about'=>'Visitor', 'activity'=>'check_in', 'type'=>'visitor', 'data' => ''],
['name'=>'Calvin Harris', 'time'=>'08:50', 'about'=>'Visitor', 'activity'=>'check_in', 'type'=>'visitor', 'data' => ''],
['name'=>'Bob Stuart', 'time'=>'08:53', 'about'=>'Visitor', 'activity'=>'check_out', 'type'=>'visitor', 'data' => ''],
['name'=>'Jane Atieno', 'time'=>'09:00', 'about'=>'Visitor', 'activity'=>'check_out', 'type'=>'visitor', 'data' => ''],
['name'=>'Allan Munyika', 'time'=>'09:05', 'about'=>'Visitor', 'activity'=>'check_in', 'type'=>'visitor', 'data' => ''],
	], 'summary' => [
		'checked_in' => 23,
		'checked_out' => 16
	    ]
    	]);

    }

}
