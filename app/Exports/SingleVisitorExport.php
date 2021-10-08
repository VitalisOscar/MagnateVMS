<?php

namespace App\Exports;

use App\Models\Site;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SingleVisitorExport implements FromArray, Responsable, ShouldAutoSize, WithStyles, WithCustomValueBinder{
    use Exportable;

    private $fileName = 'visits.xlsx';
    private $writerType = Excel::XLSX;
    private $bolds = [];

    private $visitor_id;

    function __construct($visitor_id){
        $this->visitor_id = $visitor_id;
    }

    public function array():array
    {
        $data = [];

        $r = request();

        $visitor = Visitor::whereId($this->visitor_id)->first();

        $q = $visitor->activities()
            ->with('site', 'vehicle', 'user', 'visit', 'visit.staff', 'visit.company');


        $add_site = true;

        if($r->filled('filters') && $r->get('filters') == 1){
            // Add filters
            $order = $r->get('order');

            if($order == 'past') $q->oldest('time');
            else $q->latest('time');


            if($r->filled('site')){
                $q->whereSiteId($r->get('site'));
                $site = Site::whereId($r->get('site'))->first();

                if($site != null){
                    $add_site = false;
                    array_push($data, ['Site', $site->name]);
                    array_push($this->bolds, 'A1');
                }
            }

            if($r->filled('date')){
                $date = explode(' to ', $r->get('date'));
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

            // End filters
        }

        array_push($data,
            ['Visitor', $visitor->name],
            ['Phone Number', $visitor->phone],
            ['ID Number', $visitor->id_number ? $visitor->id_number:'Not Provided'],
            ['Company From', $visitor->from],
            ['']
        );

        array_push($this->bolds, 'A'.(count($data) - 4));
        array_push($this->bolds, 'A'.(count($data) - 3));
        array_push($this->bolds, 'A'.(count($data) - 2));
        array_push($this->bolds, 'A'.(count($data) - 1));

        $headers = [
            'Site',
            'Type',
            'Date',
            'Time',
            'Reason',
            'Host',
            'Items',
            'Guard',
            'Vehicle',
            'Access Card'
        ];

        if(!$add_site){
            unset($headers[0]);
        }

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the visits
        $activities = $q->get();

        foreach($activities as $activity){

            $row = [
                $activity->site->name,
                $activity->type,
                $activity->fmt_date,
                $activity->fmt_time,
                $activity->visit->reason,
                $activity->visit->fmt_host,
                $activity->visit->items ?? '-',
                $activity->user->name,
                $activity->vehicle ? $activity->vehicle->registration_no : '-',
                $activity->visit->card_number ?? '-',
            ];

            if(!$add_site){
                unset($row[0]);
            };

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
