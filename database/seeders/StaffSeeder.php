<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Staff::create([
            'company_id' => Company::all()->random()->id,
            'name' => 'James Njoro',
            'phone' => '0740654439',
        ]);

        Staff::create([
            'company_id' => Company::all()->random()->id,
            'name' => 'Anne Njoki',
            'phone' => '0740991101',
        ]);

        Staff::create([
            'company_id' => Company::all()->random()->id,
            'name' => 'Nevis Odhiambo',
            'phone' => '0722019778',
        ]);

        Staff::create([
            'company_id' => Company::all()->random()->id,
            'name' => 'Stive Kamau',
            'phone' => '0790223011',
        ]);
    }
}
