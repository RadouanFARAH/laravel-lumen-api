<?php

namespace Database\Seeders;

use App\Models\MovementType;
use Illuminate\Database\Seeder;

class MovementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
            ['id'=>MovementType::DEPOSIT,'label'=>'DEPOSIT'],
            ['id'=>MovementType::WITHDRAWAL,'label'=>'WITHDRAWAL'],
            ['id'=>MovementType::INTERNAL_MOVEMENT,'label'=>'INTERNAL_MOVEMENT'],
        ];

        MovementType::insert($data);
    }
}
