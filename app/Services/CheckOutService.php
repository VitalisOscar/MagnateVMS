<?php

namespace App\Services;

use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckOutService{

    function checkOutVisitor($data){
        // Get the visitor
        $visitor = Visitor::where('id_number', $data['id_number'])->first();

        // visitor exists?
        if($visitor == null){
            return 'Visitor record not found. You might have entered the ID number incorrectly or you might not have checked in the visitor through the app';
        }

        // Get last visit
        $last_visit = $visitor->last_visit()->today()->stillIn()->atSite()->first();

        if($last_visit == null){
            return 'The visitor has not been checked in from your site via the app today, or has already checked out';
        }

        // Check out
        DB::beginTransaction();

        // visit
        if(!$this->checkOutVisit($last_visit, $data)){
            DB::rollBack();
            return 'Something went wrong. Retry or check out the customer manually and report if this persists';
        }

        DB::commit();
        return true;

    }

    function checkOutVisit($visit, $data){
        $visit->checked_out_by = auth('sanctum')->id();
        $visit->time_out = Carbon::now()->toDateTimeString();

        $visit->items_out = isset($data['items_out']) ? $data['items_out'] : null;

        return $visit->save();
    }
}
