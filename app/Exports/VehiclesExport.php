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

class VehiclesExport implements FromArray, Responsable, ShouldAutoSize, WithStyles, WithCustomValueBinder{
    use Exportable;

    private $fileName = 'vehicles.xlsx';
    private $writerType = Excel::XLSX;
    private $bolds = [];
    private $type = null;

    function __construct($type = null){
        $this->type = $type;
    }

    public function array():array
    {
        $data = [];

        $request = request();

        if($this->type == 'other') $q = Vehicle::otherOwned();
        else $q = Vehicle::companyOwned();

        if($request->filled('filters') && $request->get('filters') == 1){
            $order = $request->get('order');
            $show = $request->get('show');

            if($show == 'staff' && $this->type == "other"){
                $t = "Staff Owned Vehicles";
                $this->fileName = "staff_vehicles.xslx";
                $q = Vehicle::staffOwned();
            }elseif($show == 'visitors' && $this->type == "other"){
                $t = "Visitor Owned Vehicles";
                $this->fileName = "visitor_vehicles.xslx";
                $q = Vehicle::visitorOwned();
            }else{
                $t = ($this->type == "other" ? "Non-":"")."Company Owned Vehicles";
                $q = Vehicle::otherOwned();

                if($this->type == "company"){
                    $q = Vehicle::companyOwned();
                    $this->fileName = "company_vehicles.xslx";
                }
            }

            array_push($data, [$t], ['']);
            array_push($this->bolds, 'A1');

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
        }else{
            $t = ($this->type == "other" ? "Non-":"")."Company Owned Vehicles";
            $q = Vehicle::otherOwned();

            if($this->type == "company"){
                $q = Vehicle::companyOwned();
                $this->fileName = "company_vehicles.xslx";
            }

            array_push($data, [$t], ['']);
            array_push($this->bolds, 'A1');
        }

        if($this->type == "other"){
            $headers = [
                'Owner',
                'Owner Name',
                'Registration No',
            ];
        }else{
            $headers = [
                'Registration No',
                'Description',
            ];
        }

        // Add headers
        array_push($data, $headers);
        array_push($this->bolds, count($data));

        // Fetch the records
        $vehicles = $q->get();

        foreach($vehicles as $vehicle){

            if($this->type == "other"){
                $row = [
                    $vehicle->owner_type,
                    $vehicle->owner_name,
                    $vehicle->registration_no,
                ];
            }else{
                $row = [
                    $vehicle->registration_no,
                    $vehicle->description,
                ];
            }

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
