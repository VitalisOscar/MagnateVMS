<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Site::create(['name' => 'Magnate Centre']);
        Site::create(['name' => 'Mombasa Road']);
    }
}
