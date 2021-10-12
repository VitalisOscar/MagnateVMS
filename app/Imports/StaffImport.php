<?php

namespace App\Imports;

use App\Models\Staff;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StaffImport implements ToModel, WithChunkReading, WithHeadingRow{

    private $company_id;

    function __construct($company_id){
        $this->company_id = $company_id;
    }

    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new Staff([
            'name' => $row["name"] ?? $row["Name"],
            'phone' => $row["phone"] ?? $row["Phone"] ?? null,
            'department' => $row["department"] ?? $row["Department"] ?? "None",
            'extension' => $row["extension"] ??$row["Extension"] ?? null,
            'company_id' => $this->company_id
        ]);
    }

    public function chunkSize(): int
    {
        return 30;
    }
}
