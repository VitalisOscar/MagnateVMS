<?php

namespace App\Exports;

use App\Models\Visit;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

class AllVisitsExport implements FromArray, Responsable{
    use Exportable;

    private $fileName = 'all_visits.xlsx';
    private $writerType = Excel::XLSX;

    public function array():array
    {
        $headers = [
            'Site',
            'Visitor',
            'Reason',
            'Host',
            'Date',
            'Time In',
            'Items In',
            'Checked In By',
            'Time Out',
            'Items Out',
            'Checked Out By',
            'Vehicle',
            'Access Card'
        ];

        $data = [$headers];

        $r = request();

        $q = Visit::query()
            ->with([
                'visitor',
                'site',
                'staff',
                'staff.company',
                'check_in_user',
                'check_out_user'
            ]);

        $visits = $q->get();

        foreach($visits as $visit){
            $in = Carbon::createFromTimeString($visit->time_in);
            $out = $visit->time_out ? Carbon::createFromTimeString($visit->time_out):null;

            array_push($data, [
                $visit->site->name,
                $visit->visitor->name,
                $visit->reason,
                $visit->host,
                $in->format('Y-m-d'),
                $in->format('H:i'),
                $visit->items_in,
                $visit->check_in_user->name,
                $out ? $out->format('H:i'):'',
                $visit->items_out,
                $visit->check_out_user ? $visit->check_out_user->name:null,
                $visit->car_registration,
                $visit->card_number,
            ]);
        }

        return $data;
    }
}
