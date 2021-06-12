<?php

namespace App\Services;

use App\Models\Vehicle;

class VehicleService{
    function getOrAddVehicle($registration_no, $type, $id){
        $slug = strtoupper(str_replace(' ', '', $registration_no));

        $vehicle = Vehicle::whereSlug($slug)
            ->whereVehicleableType($type)
            ->whereVehicleableId($id)
            ->first();

        if($vehicle != null){
            return $vehicle;
        }

        $vehicle = new Vehicle([
            'registration_no' => preg_replace('/ +/', ' ', $registration_no),
            'description' => \Illuminate\Support\Str::title($type).' vehicle',
            'vehicleable_id' => $id,
            'vehicleable_type' => $type,
            'slug' => $slug
        ]);

        if($vehicle->save()){
            return $vehicle;
        }

        return false;
    }
}
