<?php

namespace App\Http\Controllers\Api\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccessCardController extends Controller
{
    function getVisitorsWithoutCard(){
        return $this->json->mixed(null,
            Visitor::whereHas('visits', function($q){
                    $q->stillIn()->atSite()->today()->noCardIssued();
                })
                ->with('last_visit')
                ->get()
                ->each(function ($visitor){
                    $visit = $visitor->last_visit;

                    $visit->staff_name = $visit->staff->name;
                    $visit->company = $visit->staff->company->name;

                    $visitor->reason = $visit->reason;
                    $visitor->from = $visit->from;
                    $visitor->visitor_id = $visitor->id;

                    $in = Carbon::createFromTimeString($visit->time_in)->format('H:i');

                    $visit->time_in = $in;
                    $visitor->visit = $visit;
                })
            );
    }

    function issueCard(Request $request){
        $validator = validator($request->post(), [
            'visit_id' => 'required|exists:visits,id',
            'card_number' => 'required'
        ],[
            'visit_id.required' => 'Please select a visitor from the given list',
            'visit_id.exists' => 'Please select a visitor from the given list',
            'card_number' => 'Please enter the card number you issued to the visitor',
        ]);

        if($validator->fails()){
            return $this->json->errors($validator->errors()->all());
        }

        $visit = Visit::whereId($request->post('visit_id'))
            ->stillIn()
            ->atSite()
            ->today()
            ->noCardIssued()
            ->first();

        if($visit == null){
            return $this->json->error('It looks like a card has already been issued to the visitor. Please refresh your app ad try again');
        }

        $visit->card_number = $request->post('card_number');

        if($visit->save()){
            return $this->json->success('Done. The access card issued has been noted');
        }

        return $this->json->error('Oops. Something went wrong. Please try again or report to IT if error persists');
    }
}
