<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CitiesSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\City::insert(
            json_decode(file_get_contents(public_path('private/final-cities.json')), true)
        );
    }
}
