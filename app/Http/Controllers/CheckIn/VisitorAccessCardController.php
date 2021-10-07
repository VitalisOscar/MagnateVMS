<?php

namespace App\Http\Controllers\CheckIn;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitorAccessCardController extends Controller
{
    function getVisitorsWithoutCard()
    {
        $query = Visitor::whereHas('last_activity', function ($a) {
            $a->onDate(Carbon::today())
                ->atSite(auth('sanctum')->user()->site_id)
                ->checkIn()
                ->whereHas('visit', function ($q) {
                    $q->noCardIssued();
                });
        })
            ->with([
                'last_activity', 'last_activity.visit',
                'last_activity.vehicle', 'last_activity.visit.company', 'last_activity.visit.staff'
            ]);

        $result = new ResultSet($query);

        return $this->json->mixed(null, $result->items);
    }

    function issueCard(Request $request)
    {
        $validator = validator($request->post(), [
            'visit_id' => 'required|exists:visits,id',
            'card_number' => 'required'
        ], [
            'visit_id.required' => 'Please select a visitor from the given list',
            'visit_id.exists' => 'Please select a visitor from the given list',
            'card_number' => 'Please enter the card number you issued to the visitor',
        ]);

        if ($validator->fails()) {
            return $this->json->errors($validator->errors()->all());
        }

        $visit = Visit::whereId($request->post('visit_id'))
            ->noCardIssued()
            ->with('activity', 'activity.by')
            ->first();

        if ($visit == null) {
            return $this->json->error('It looks like a card has already been issued to the visitor. Please refresh your app ad try again');
        }

        $visit->card_number = $request->post('card_number');

        if ($visit->save()) {
            return $this->json->success('Done. The access card issued to '.$visit->activity->by->first_name.' has been captured in the system');
        }

        return $this->json->error('Oops. Something went wrong. Please try again or report to IT if error persists');
    }
}
