<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Drive;
use App\Models\Site;
use App\Models\StaffCheckIn;
use App\Models\Vehicle;
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

class CompanyVehiclesExport implements FromArray, Responsable, ShouldAutoSize, WithStyles, WithCustomValueBinder{
    use Exportable;

    private $fileName = 'company_vehicles.xlsx';
    private $writerType = Excel::XLSX;
    private $bolds = [];

    public function array():array
    {
        $data = [];

        $request = request();

        $q = Drive::with('vehicle', 'driveable_in', 'driveable_out');

        $q = Vehicle::companyOwned();

        if($request->filled('filters') && $request->get('filters') == 1){
            $order = $request->get('order');

            if($request->filled('keyword')){
                $k = '%'.$request->get('keyword').'%';

                $q->where(function($q1) use($k){
                    $q1->where('registration_no', 'like', $k)
                        ->orWhere('description', 'like', $k)
                        ->orWhereHas('vehicleable', function($q2) use($k){
                            $q2->where('name', 'like', $k);
                        });
                });

                array_push($data, ['Criteria', $request->get('keyword')], ['']);
                array_push($this->bolds, 'A'.(count($data) - 1));
            }

            if($order == 'az') $q->orderBy('registration_no', 'ASC');
            elseif($order == 'za') $q->orderBy('registration_no', 'DESC');
        }

        $headers = [
            'Owner',
            'Owner Name',
            'Registration No',
        ];

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the records
        $vehicles = $q->get();

        foreach($vehicles as $vehicle){

            $row = [
                $vehicle->owner_type,
                $vehicle->owner_name,
                $vehicle->registration_no,
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
