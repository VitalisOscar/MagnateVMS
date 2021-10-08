<?php

namespace App\Exports;

use App\Models\Login;
use App\Models\Site;
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

class LoginsExport implements FromArray, Responsable, ShouldAutoSize, WithStyles, WithCustomValueBinder{
    use Exportable;

    private $fileName = 'logins.xlsx';
    private $writerType = Excel::XLSX;
    private $bolds = [];

    public function array():array
    {
        $data = [];

        $request = request();


        $q = Login::where('user_id', '<>', null)->with('user', 'site');

        $add_site = true;
        if($request->filled('filters') && $request->get('filters') == 1){
            $order = $request->get('order');

            if($order == 'oldest') $q->oldest('time');
            else $q->latest('time');

            if($request->filled('site')){
                $q->whereSiteId($request->get('site'));

                $site = Site::whereId($request->get('site'))->first();

                if($site != null){
                    $add_site = false;
                    array_push($data, ['Site', $site->name]);
                    array_push($this->bolds, 'A1');
                }
            }

            if($request->filled('type')){
                $q->whereUserType($request->get('type'));

                array_push($data, ['Type', \Illuminate\Support\Str::title($request->get('type'))], ['']);
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

            if($request->filled('keyword')){
                $keyword = "%".$request->get('keyword')."%";
                $q->whereHas('user', function($q2) use ($keyword){
                    $q2->where('name', 'like', $keyword);
                });

                array_push($data, ['Criteria', $request->get('keyword')], ['']);
                array_push($this->bolds, 'A'.(count($data) - 1));
            }
        }


        $headers = [
            'Type',
            'Account',
            'Date',
            'Time',
            'Site',
            'Outcome',
        ];

        if(!$add_site){
            unset($headers[4]);
        }

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the visits
        $logins = $q->get();

        foreach($logins as $login){
            $t = Carbon::createFromTimeString($login->time);

            $row = [
                $login->user_type,
                $login->user->name,
                $login->fmt_date,
                $login->fmt_time,
                $login->site->name,
                \Illuminate\Support\Str::title($login->status)
            ];

            if(!$add_site){
                unset($row[4]);
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
