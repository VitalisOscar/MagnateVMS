<?php

namespace App\Exports;

use App\Models\Activity;
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

        $q = Activity::byCompanyVehicle()
            ->with('by', 'driver_task', 'driver_task.driver', 'user', 'site');

        if($request->filled('filters') && $request->get('filters') == 1){
            $order = $request->get('order');

            if($order == 'past') $q->oldest('time');
            else $q->latest('time');


            if($request->filled('keyword')){
                $q->where(function($q1) use($request){
                    $q1->whereHas('driver_task', function($dt) use($request){
                        $dt->whereHas('driver', function($d) use($request){
                            $k = '%'.$request->get('keyword').'%';
                            $d->where('name', 'like', $k);
                        });
                    })
                    ->orWhereHas('companyVehicle', function($v) use($request){
                        $k = '%'.$request->get('keyword').'%';
                        $v->where('registration_no', 'like', $k);
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

                $q->whereDate('time', '>=', $from)
                    ->whereDate('time', '<=', $to);
            }
        }

        $headers = [
            'Reg No',
            'Description',
            'Site',
            'Type',
            'Date',
            'Time',
            'Task',
            'Driver',
            'Mileage',
            'Guard',
        ];

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the records
        $activities = $q->get();

        foreach($activities as $activity){
            $row = [
                $activity->by->registration_no,
                $activity->by->description,
                $activity->site->name,
                $activity->type,
                $activity->fmt_date,
                $activity->fmt_time,
                $activity->driver_task->task,
                $activity->driver_task->driver->name,
                $activity->driver_task->fmt_mileage,
                $activity->user->name,
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
