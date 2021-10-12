<?php

namespace App\Imports;

use App\Models\Driver;
use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DriversImport implements ToModel, WithChunkReading, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Driver|null
     */
    public function model(array $row)
    {
        return new Driver([
            'name' => $row["name"] ?? $row['driver'],
            'department' => $row["department"] ?? 'No Department',
            'phone' => $row["phone"] ?? '',
        ]);
    }

    public function chunkSize(): int
    {
        return 30;
    }
}
