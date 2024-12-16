<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AirportsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Airport::insert(
            json_decode(file_get_contents(public_path('private/final-airports.json')), true)
        );
    }
}
