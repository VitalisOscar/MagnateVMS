<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffCheckIn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffRecordsController extends Controller
{
    function __invoke(Request $request){
        $date = $request->filled('date') ?
            $request->get('date'):
            Carbon::today()->format('Y-m-d');

        $q = StaffCheckIn::atSite()
            ->whereDate('time_in', $date);

        $total = $q->count();

        // paginate
        $page = $request->filled('page') ?
            intval($request->get('page')) : 1;

        $limit = 15;
        $offset = $limit * ($page - 1);

        $q->limit($limit)->offset($offset);

        $last_page = ceil($total/$limit);


        $q->with('staff', 'staff.company');

        $res = $q->get()->each(function($checkin){
            $staff = $checkin->staff;
            $company = $staff->company;

            $checkin->name = $staff->name;
            $checkin->company_name = $company->name;

            $checkin->time_in = Carbon::createFromTimeString($checkin->time_in)->format('H:i');
            $checkin->time_out = $checkin->time_out == null ? null:Carbon::createFromTimeString($checkin->time_out)->format('H:i');

        });

        return $this->json->mixed([
            'total' => $total,
            'last_page' => $last_page,
            'date' => $date
        ], $res);
    }
}
