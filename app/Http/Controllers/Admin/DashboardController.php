<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Staff;
use App\Models\StaffCheckIn;
use App\Models\StaffLog;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{

    function __invoke(Request $request){
        $date = $request->filled('date') ? $request->get('date') : Carbon::today()->format('Y-m-d');
        $site = $request->filled('site') ? $request->get('site') : false;

        $summaries_fetch = DB::select(
            'select ('.
            Visit::whereHas('vehicle')
                ->whereRaw("date(time_in) = '".$date."'")
                ->when($site, function($q, $site){
                    return $q->whereRaw("site_id = $site");
                })
                ->selectRaw('count(*)')
                ->toSql().
                ') as visit_vehicles, ('.

            StaffCheckIn::whereHas('vehicle')
                ->whereRaw("date(time_in) = '".$date."'")
                ->when($site, function($q, $site){
                    return $q->whereRaw("site_id = $site");
                })
                ->selectRaw('count(*)')
                ->toSql().
                ') as staff_vehicles, ('.

            Visitor::whereHas('visits', function($q) use($date, $site){
                    $q->whereRaw("date(time_in) = '".$date."'")
                    ->when($site, function($q, $site){
                        return $q->whereRaw("site_id = $site");
                    });
                })
                ->selectRaw('count(*)')
                ->toSql().
                ') as visitors, ('.

            Staff::whereHas('check_ins', function($q) use($date, $site){
                    $q->whereRaw("date(time_in) = '".$date."'")
                        ->when($site, function($q, $site){
                            return $q->whereRaw("site_id = $site");
                        });
                })
                ->selectRaw('count(*)')
                ->toSql().
                ') as staff'
        );

        $summaries_fetch = $summaries_fetch[0];

        $visit_activity = DB::select(
            Visit::whereRaw("date(time_in) = '".$date."'")
            ->when($site, function($q, $site){
                return $q->whereRaw("site_id = $site");
            })
            ->groupBy(Visit::query()->raw('HOUR(time_in)'))
            ->selectRaw('count(*) as count, HOUR(time_in) as hour')
            ->toSql()
        );

        $staff_activity = DB::select(
            StaffCheckIn::whereRaw("date(time_in) = '".$date."'")
            ->when($site, function($q, $site){
                return $q->whereRaw("site_id = $site");
            })
            ->groupBy(Visit::query()->raw('HOUR(time_in)'))
            ->selectRaw('count(*) as count, HOUR(time_in) as hour')
            ->toSql()
        );

        // return $activity_fetch;

        $activity_data = [];

        foreach ($visit_activity as $a){
            $hour_activity = $activity_data[$a->hour] ?? [];
            $hour_activity['visitors'] = $a->count;
            $activity_data[$a->hour] = $hour_activity;
        }

        foreach ($staff_activity as $a){
            $hour_activity = $activity_data[$a->hour] ?? [];
            $hour_activity['staff'] = $a->count;
            $activity_data[$a->hour] = $hour_activity;
        }

        $summaries = [
            [
                'label' => 'Visitors',
                'value' => $summaries_fetch->visitors,
                'color' => '#2dce89'
            ],
            [
                'label' => 'Staff',
                'value' => $summaries_fetch->staff,
                'color' => '#5e72e4'
            ],
            [
                'label' => 'Vehicles Used',
                'value' => ($summaries_fetch->staff_vehicles + $summaries_fetch->visit_vehicles),
                'color' => 'coral'
            ],
        ];

        $totals = [
            'visitors' => Visitor::count(),
            'sites' => Site::count(),
            'users' => User::count(),
        ];

        return view('admin.dashboard', [
            'activity_data' => $activity_data,
            'summaries' => $summaries,
            'totals' => $totals
        ]);
    }

}
