<?php

namespace App\Exports;

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

        if($this->vehicle->isStaffVehicle()){
            array_push($data,
                ['Vehicle Owner:', 'Staff ('.$this->vehicle->owner->company->name.')'],
                ['Owner Name:', $this->vehicle->owner->name],
                ['Contact:', $this->vehicle->owner->phone ?? $this->vehicle->owner->extension ?? ' '],
                ['Reg Number', $this->vehicle->registration_no],
                ['']
            );
        }else{
            array_push($data,
                ['Vehicle Owner:', 'Visitor'],
                ['Owner Name:', $this->vehicle->owner->name],
                ['Contact:', $this->vehicle->owner->phone ?? ' '],
                ['Reg Number', $this->vehicle->registration_no],
                ['']
            );
        }

        array_push($this->bolds, 'A'.(count($data) - 4));
        array_push($this->bolds, 'A'.(count($data) - 3));
        array_push($this->bolds, 'A'.(count($data) - 2));
        array_push($this->bolds, 'A'.(count($data) - 1));

        $q = $this->vehicle
            ->usages()
            ->with('site', 'user');

        if($request->filled('filters') && $request->get('filters') == 1){

            // Activity
            $order = $request->get('order');

            if($order == 'past') $q->oldest('time');
            else $q->latest('time');


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

                $q->whereDate('time', '>=', $from)
                    ->whereDate('time', '<=', $to);
            }

        }


        $headers = [
            'Site',
            'Type',
            'Date',
            'Time',
            'Guard'
        ];

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the visits
        $activities = $q->get();

        foreach($activities as $a){
            $row = [
                $a->site->name,
                $a->type,
                $a->fmt_date,
                $a->fmt_time,
                $a->user->name,
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
