<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountrySeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Country::insert(
            json_decode(file_get_contents(public_path('private/final-countries.json')), true)
        );
    }
}
