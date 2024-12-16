<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [[
            "id" => Role::ROOT,
            "label" => "Root",
        ], [
            "id" => Role::ADMIN,
            "label" => "Admin",
        ], [
            "id" => Role::USER,
            "label" => "User",
        ]];

        Role::insert($data);
    }
}
