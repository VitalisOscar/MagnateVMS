<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'john',
            'name' => 'John Doe',
            'site_id' => Site::where('name', 'Magnate Centre, Westlands')->first()->id,
            'password' => Hash::make('password')
        ]);

        User::create([
            'username' => 'alice',
            'name' => 'Alice Brown',
            'site_id' => Site::where('name', 'Mombasa Road')->first()->id,
            'password' => Hash::make('password')
        ]);

        User::create([
            'username' => 'brian',
            'name' => 'Brian Mwaura',
            'site_id' => Site::where('name', 'Magnate Centre, Westlands')->first()->id,
            'password' => Hash::make('password')
        ]);

        User::create([
            'username' => 'jane',
            'name' => 'Jane Doe',
            'site_id' => Site::where('name', 'Mombasa Road')->first()->id,
            'password' => Hash::make('password')
        ]);
    }
}
