<?php

namespace App\Exports;

use App\Models\Visitor;
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

class AllVisitorsExport implements FromArray, Responsable, ShouldAutoSize, WithStyles, WithCustomValueBinder{
    use Exportable;

    private $fileName = 'visitors.xlsx';
    private $writerType = Excel::XLSX;
    private $bolds = [];

    public function array():array
    {
        $data = [];

        $request = request();

        $q = Visitor::query()->with('last_activity', 'last_activity.site');

        $add_site = true;

        if($request->filled('filters') && $request->get('filters') == 1){
            // Add filters
            if($request->filled('keyword')){
                $keyword = "%".$request->get('keyword')."%";
                $q->where(function($q1) use($keyword){
                    $q1->where('name', 'like', $keyword)
                        ->orWhere('id_number', 'like', $keyword)
                        ->orWhere('phone', 'like', $keyword)
                        ->orWhere('from', 'like', $keyword);
                });

                array_push($data, ['Criteria', $request->get('keyword')], ['']);
                array_push($this->bolds, 'A1');
            }

            $order = $request->get('order');
            if($order == 'recent') $q->latest();
            elseif($order == 'az') $q->orderBy('name', 'ASC');
            elseif($order == 'za') $q->orderBy('name', 'DESC');
            // End filters
        }

        $headers = [
            'Name',
            'Phone Number',
            'Company From',
            'ID Number',
            'Last Visit',
            'Last Site Visited',
        ];

        if(!$add_site){
            unset($headers[0]);
        }

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the visits
        $visitors = $q->get();

        foreach($visitors as $visitor){
            $row = [
                $visitor->name,
                $visitor->phone,
                $visitor->from,
                $visitor->id_number,
                $visitor->last_activity ? $visitor->last_activity->fmt_datetime:'No Visits',
                $visitor->last_activity ? $visitor->last_activity->site->name:'No Visits'
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
