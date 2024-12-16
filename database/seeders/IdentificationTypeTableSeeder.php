<?php

namespace Database\Seeders;

use App\Models\IdentificationType;
use Illuminate\Database\Seeder;

class IdentificationTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [[
            "id" => IdentificationType::ID_CARD,
            "label" => "ID CARD",
        ], [
            "id" => IdentificationType::PASSPORT,
            "label" => "PASSPORT",
        ]];

        IdentificationType::insert($data);
    }
}
