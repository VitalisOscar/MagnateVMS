<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Site;
use App\Models\Staff;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    function __invoke(Request $request){
        $date = $request->filled('date') ? $request->get('date') : Carbon::today()->format('Y-m-d');
        $site = $request->filled('site') ? $request->get('site') : false;

        $summaries_fetch = DB::select(
            'select ('.
            $this->getSql(
                Vehicle::whereHas('usages', function($a) use($date, $site){
                    $a->whereRaw("date(time) = '".$date."'")
                        ->when($site, function($q, $site){
                            return $q->whereRaw("site_id = $site");
                        });
                })
                ->selectRaw('count(*)')
            ).') as vehicles_used, ('.

            $this->getSql(
                Visitor::whereHas('activities', function($a) use($date, $site) {
                    $a->whereRaw("date(time) = '".$date."'")
                        ->when($site, function($s, $site){
                            return $s->whereRaw("site_id = $site");
                        });
                })
                ->selectRaw('count(*)')
            ).') as visitors, ('.

            $this->getSql(
                Staff::whereHas('activities', function($q) use($date, $site){
                    $q->whereRaw("date(time) = '".$date."'")
                        ->when($site, function($s, $site){
                            return $s->whereRaw("site_id = $site");
                        });
                })
                ->selectRaw('count(*)')
            ).') as staff'
        );

        $summaries_fetch = $summaries_fetch[0];

        $visit_activity = DB::select(
            $this->getSql(
                Activity::byVisitor()
                ->whereRaw("date(time) = '".$date."'")
                ->when($site, function($q, $site){
                    return $q->whereRaw("site_id = $site");
                })
                ->groupBy(Activity::query()->raw('HOUR(time)'))
                ->selectRaw('count(*) as count, HOUR(time) as hour')
            )
        );

        $staff_activity = DB::select(
            $this->getSql(
                Activity::byStaff()
                ->whereRaw("date(time) = '".$date."'")
                ->when($site, function($q, $site){
                    return $q->whereRaw("site_id = $site");
                })
                ->groupBy(Activity::query()->raw('HOUR(time)'))
                ->selectRaw('count(*) as count, HOUR(time) as hour')
            )
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
                'value' => ($summaries_fetch->vehicles_used),
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

    /**
     * @param Builder $query
     */
    function getSql($query){
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function($binding){
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

}
