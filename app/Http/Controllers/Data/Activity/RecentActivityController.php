<?php

namespace App\Http\Controllers\Data\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Staff;
use App\Models\Vehicle;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecentActivityController extends Controller
{

    function __invoke(Request $request){
        $visitors = Visitor::whereHas('last_activity', function($visit) {
            $visit->onDate(Carbon::today())
                ->atSite(auth('sanctum')->user()->site_id);
        })
        ->count();

        $staff = Staff::whereHas('last_activity', function($activity) {
            $activity->onDate(Carbon::today())
                ->atSite(auth('sanctum')->user()->site_id);
        })
        ->count();

        $vehicles = Vehicle::otherOwned()
        ->whereHas('owner', function($owner) {
            $owner->whereHas('last_activity', function($activity) {
                $activity->onDate(Carbon::today())
                    ->atSite(auth('sanctum')->user()->site_id);
            });
        })
        ->count();

        $activity = Activity::latest('time')
            ->onDate(Carbon::today())
            ->atSite(auth('sanctum')->user()->site_id)
            ->limit(7)
            ->with('by', 'visit')
            ->get();

        return $this->json->data([
            'activity' => $activity,
            'summary' => [
                'visitors' => $visitors,
                'vehicles' => $vehicles,
                'staff' => $staff,
                'staff' => $staff
            ]
        ]);

    }
}
