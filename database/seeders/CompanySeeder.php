<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Site;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'name' => 'MVL',
            'site_id' => Site::where('name', 'Mombasa Road')->first()->id,
        ]);

        Company::create([
            'name' => 'MVL',
            'site_id' => Site::where('name', 'Magnate Centre, Westlands')->first()->id,
        ]);

        Company::create([
            'name' => 'Other Company',
            'site_id' => Site::where('name', 'Magnate Centre, Westlands')->first()->id,
        ]);
    }
}
