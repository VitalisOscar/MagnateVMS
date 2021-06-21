<?php

namespace App\Imports;

use App\Models\Staff;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StaffImport implements ToModel, WithChunkReading{

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
            'name' => $row[0],
            'phone' => $row[1],
            'company_id' => $this->company_id
        ]);
    }

    public function chunkSize(): int
    {
        return 30;
    }
}
