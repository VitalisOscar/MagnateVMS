<?php

namespace App\Http\Controllers\Admin\History;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginHistoryController extends Controller
{
    function __invoke(Request $request){
        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $order = $request->get('order');

        $q = Login::query()->with('user', 'admin', 'site');
        if($order == 'oldest') $q->oldest('time');
        else $q->latest('time');

        return response()->view('admin.history.logins',[
            'result' => new ResultSet($q, $limit)
        ]);
    }
}
