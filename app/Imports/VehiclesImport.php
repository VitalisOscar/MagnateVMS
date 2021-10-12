<?php

namespace App\Imports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VehiclesImport implements ToModel, WithChunkReading, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Vehicle|null
     */
    public function model(array $row)
    {
        $reg = trim(($row['registration_no'] ?? $row['reg_no']));
        
        return new Vehicle([
            'registration_no' => strtoupper(preg_replace('/ +/', ' ', $reg)),
            'description' => $row["description"] ?? $row["type"] ?? $row["Description"] ?? "No Description",
            'owner_id' => 0,
            'owner_type' => Vehicle::OWNER_COMPANY,
        ]);
    }

    public function chunkSize(): int
    {
        return 30;
    }
}
