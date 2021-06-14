<?php

namespace App\Http\Controllers\Api\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetDriversController extends Controller
{
    function __invoke(Request $request){
        return $this->json->mixed(null, Driver::all());
    }
}
