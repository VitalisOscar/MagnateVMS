<?php

namespace App\Http\Controllers\Api\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Drive;
use App\Models\Vehicle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetVehiclesController extends Controller
{
    function company(Request $request){
        $vehicles = Vehicle::companyOwned()->get()->each(function ($vehicle){
            $vehicle->owned_by = 'Company';
            $vehicle->owner_name = 'Company';
        });

        return $this->json->mixed(null, $vehicles);
    }
}
