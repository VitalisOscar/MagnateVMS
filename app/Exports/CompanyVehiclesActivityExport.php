<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Drive;
use App\Models\Site;
use App\Models\StaffCheckIn;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyVehiclesActivityExport implements FromArray, Responsable, ShouldAutoSize, WithStyles, WithCustomValueBinder{
    use Exportable;

    private $fileName = 'company_vehicles_activity.xlsx';
    private $writerType = Excel::XLSX;
    private $bolds = [];

    public function array():array
    {
        $data = [];

        $request = request();


        $limit = intval($request->get('limit'));
        if(!in_array($limit, [15,30,50,100])) $limit = 15;

        $q = Drive::with('vehicle', 'driveable_in', 'driveable_out');

        if($request->filled('filters') && $request->get('filters') == 1){
            $order = $request->get('order');

            if($order == 'past') $q->oldest('time_in');
            else $q->latest('time_in');


            if($request->filled('keyword')){
                $q->where(function($q1) use($request){
                    $q1->whereHas('vehicle', function($q2) use($request){
                        $k = '%'.$request->get('keyword').'%';
                        $q2->where('registration_no', 'like', $k)
                            ->orWhere('description', 'like', $k);
                    })
                    ->orWhereHas('driveable_in', function($q2) use($request){
                        $k = '%'.$request->get('keyword').'%';
                        $q2->where('name', 'like', $k);
                    });
                });

                array_push($data, ['Criteria', $request->get('keyword')], ['']);
                array_push($this->bolds, 'A'.(count($data) - 1));
            }

            if($request->filled('date')){
                $date = explode(' to ', $request->get('date'));
                if(count($date) == 1){
                    $from = $date[0];
                    $to = $date[0];

                    array_push($data, ['Date', $from], ['']);
                    array_push($this->bolds, 'A'.(count($data) - 1));
                }else{
                    $from = $date[0];
                    $to = $date[1];

                    if($from > $to){
                        $x = $from;
                        $from = $to;
                        $to = $x;
                    }

                    array_push($this->bolds, 'A'.(count($data) + 1));
                    array_push($this->bolds, 'A'.(count($data) + 2));
                    array_push($data, ['From', $from], ['To', $to], ['']);
                }

                $q->whereDate('time_in', '>=', $from)
                    ->whereDate('time_in', '<=', $to);
            }
        }

        $headers = [
            'Vehicle Reg No',
            'Vehicle Description',
            'Date Out',
            'Time Out',
            'Checked Out By',
            'Driver Out',
            'Fuel Out',
            'Fuel In',
            'Mileage Out',
            'Mileage In',
            'Date In',
            'Time In',
            'Checked In By',
            'Driver In',
        ];

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the records
        $checks = $q->get();

        foreach($checks as $check){
            $in = $check->time_in ? Carbon::createFromTimeString($check->time_in):null;
            $out = $check->time_out ? Carbon::createFromTimeString($check->time_out):null;

            $row = [
                $check->vehicle->registration_no,
                $check->vehicle->description,
                $out ? $out->format('Y-m-d'):'',
                $out ? $out->format('H:i'):'',
                $check->check_out_user ? $check->check_out_user->name:'-',
                $check->driveable_out ? ($check->driveable_out->name.($check->driveable_out->department ? ' - '.$check->driveable_out->department:' (Staff)')):'Not Captured',
                $check->fuel_out,
                $check->fuel_in ? $check->fuel_in : 'Not Captured',
                $check->mileage_out,
                $check->mileage_in ? $check->mileage_in : 'Not Captured',
                $in ? $in->format('Y-m-d'):'',
                $in ? $in->format('H:i'):'',
                $check->check_in_user? $check->check_in_user->name:'-',
                $check->driveable_in ? ($check->driveable_in->name.($check->driveable_in->department ? ' - '.$check->driveable_in->department:' (Staff)')):'Not Captured',
            ];

            array_push($data, $row);
        }

        return $data;
    }

    public function styles(Worksheet $sheet):array
    {
        $styles = [];

        foreach($this->bolds as $bold){
            $styles[$bold] = ['font' => ['bold' => true]];
        }

        return $styles;
    }

    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit($value, DataType::TYPE_STRING);
        return true;
    }
}
