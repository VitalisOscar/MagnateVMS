<?php

namespace App\Exports;

use App\Models\Company;
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

class AllStaffCheckInsExport implements FromArray, Responsable, ShouldAutoSize, WithStyles, WithCustomValueBinder{
    use Exportable;

    private $fileName = 'all_checkins.xlsx';
    private $writerType = Excel::XLSX;
    private $bolds = [];

    public function array():array
    {
        $data = [];

        $r = request();

        $q = StaffCheckIn::query()
            ->with('staff', 'staff.company', 'site', 'check_in_user', 'check_out_user');

        $add_site = true;
        $add_company = true;

        if($r->filled('filters') && $r->get('filters') == 1){
            // Add filters
            $order = $r->get('order');


            $order = $r->get('order');

            if($order == 'past') $q->oldest('time_in');
            else $q->latest('time_in');

            if($r->filled('site')){
                $q->where('site_id', $r->get('site'));

                $site = Site::whereId($r->get('site'))->first();

                if($site != null){
                    $add_site = false;
                    array_push($data, ['Site', $site->name]);
                    array_push($this->bolds, 'A1');
                }
            }

            if($r->filled('company')){
                $q->whereHas('staff', function($q1) use($r){
                    $q1->whereCompanyId($r->get('company'));
                });

                $company = Company::whereId($r->get('company'))
                    ->with('site')
                    ->first();

                if($company != null){
                    $add_company = false;
                    array_push($data, ['Company From', $company->name.' (at '.$company->site->name.')']);
                    array_push($this->bolds, 'A'.count($data));
                    array_push($data, ['']);
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

                $q->whereDate('time_in', '>=', $from)
                    ->whereDate('time_in', '<=', $to);
            }

            // End filters
        }

        $headers = [
            'Site',
            'Staff Name',
            'Phone Number',
            'Company',
            'Date',
            'Time In',
            'Checked In By',
            'Time Out',
            'Checked Out By',
            'Vehicle'
        ];

        if(!$add_site) unset($headers[0]);
        if(!$add_company) unset($headers[3]);

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the visits
        $checkins = $q->get();

        foreach($checkins as $checkin){
            $in = Carbon::createFromTimeString($checkin->time_in);
            $out = $checkin->time_out ? Carbon::createFromTimeString($checkin->time_out):null;

            $row = [
                $checkin->site->name,
                $checkin->staff->name,
                $checkin->staff->phone,
                $checkin->staff->company->name,
                $in->format('Y-m-d'),
                $in->format('H:i'),
                $checkin->check_in_user->name,
                $out ? $out->format('H:i'):'',
                $checkin->check_out_user ? $checkin->check_out_user->name:null,
                $checkin->car_registration,
            ];

            if(!$add_site) unset($row[0]);
            if(!$add_company) unset($row[3]);

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
