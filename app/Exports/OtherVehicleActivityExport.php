<?php

namespace App\Exports;

use App\Models\StaffCheckIn;
use App\Models\Visit;
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

class OtherVehicleActivityExport implements FromArray, Responsable, ShouldAutoSize, WithStyles, WithCustomValueBinder{
    use Exportable;

    private $fileName = 'vehicle_usage.xlsx';
    private $writerType = Excel::XLSX;
    private $bolds = [];

    private $vehicle = null;

    function __construct($vehicle){
        $this->vehicle = $vehicle;
    }

    public function array():array
    {
        $data = [];

        $request = request();

        if($this->vehicle->vehicleable_type == 'staff'){
            $q = StaffCheckIn::whereVehicleId($this->vehicle->id);

            array_push($data,
                ['Vehicle Owner:', 'Staff ('.$this->vehicle->vehicleable->company->name.')'],
                ['Owner Name:', $this->vehicle->owner_name],
                ['Reg Number', $this->vehicle->registration_no],
                ['']
            );
        }else{
            $q = Visit::whereVehicleId($this->vehicle->id);

            array_push($data,
                ['Vehicle Owner:', 'Visitor'],
                ['Owner Name:', $this->vehicle->owner_name],
                ['Reg Number', $this->vehicle->registration_no],
                ['']
            );
        }

        array_push($this->bolds, 'A'.(count($data) - 3));
        array_push($this->bolds, 'A'.(count($data) - 2));
        array_push($this->bolds, 'A'.(count($data) - 1));

        $q->with('site', 'check_in_user', 'check_out_user');

        if($request->filled('filters') && $request->get('filters') == 1){

            // Activity
            $order = $request->get('order');

            if($order == 'past') $q->oldest('time_in');
            else $q->latest('time_in');


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
            'Site',
            'Date',
            'Time In',
            'Checked In By',
            'Time Out',
            'Checked Out By',
        ];

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the visits
        $activities = $q->get();

        foreach($activities as $a){
            $in = Carbon::createFromTimeString($a->time_in);
            $out = $a->time_out ? Carbon::createFromTimeString($a->time_out):null;

            $row = [
                $a->site->name,
                $in->format('Y-m-d'),
                $in->format('H:i'),
                $a->check_in_user ? $a->check_in_user->name : null,
                $out ? $out->format('H:i'):'',
                $a->check_out_user ? $a->check_out_user->name:null,
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
