<?php

namespace App\Services\Traits;

use App\Models\Item;

trait CapturesItems{
    function checkInItems($visit, $data){
        if(isset($data['items'])){
		foreach($data['items'] as $i){
		    $item = new Item([
		        'visit_id' => $visit->id,
		        'name' => $i['name'],
		        'notes' => $i['notes'],
		        'in' => true, // being checked in
		        'out' => false
		    ]);

		    if(!$item->save()) return false;
		}
	}

        return true;
    }

    /**
     * Check out items which were not checked in while entering premises
     */
    function checkOutMoreItems($visit, $data){
        // Items is an array of data of items being checked out
        foreach($data['more_items'] as $i){
            $item = new Item([
                'visit_id' => $visit->id,
                'name' => $i['name'],
                'notes' => $i['notes'],
                'in' => false,
                'out' => true, // being checked out
                'pass_number' => $i['pass_number']
            ]);

            if(!$item->save()) return false;
        }

        return true;
    }

    function checkOutCheckedInItems($visit, $data){
        // Items is an array of ids of checked in items being checked out
        foreach($data['checked_in_items'] as $i){
            $item = Item::where('id', $i)
                ->where('visit_id', $visit->id)
                ->first();

            if($item == null) return false;

            $item->out = true;

            if(!$item->save()) return false;
        }

        return true;
    }
}
