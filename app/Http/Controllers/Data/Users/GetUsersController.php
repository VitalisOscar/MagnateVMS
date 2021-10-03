<?php

namespace App\Http\Controllers\Data\Users;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetUsersController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');

        $q = User::query()->with('last_login', 'last_login.site');
        if($order == 'recent') $q->latest();
        elseif($order == 'az') $q->orderBy('name', 'ASC');
        elseif($order == 'za') $q->orderBy('name', 'DESC');

        return response()->view('admin.users.all',[
            'result' => new ResultSet($q, $limit, function($user){
                $c = $user->created_at;
                $user->time = substr($c->monthName, 0, 3).' '.$c->day;

                if(!$c->isCurrentYear()){
                    $user->time .= ', '.$c->year;
                }

                return $user;
            })
        ]);
    }
}
