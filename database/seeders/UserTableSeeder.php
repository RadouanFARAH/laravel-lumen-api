<?php

namespace Database\Seeders;

use App\Models\IdentificationType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                "id" => User::LUGGIN,
                "last_name" => "Root",
                "first_name" => "User",
                "email" => "root@luggin.io",
                "phone" => "1xxxxxxxxx1",
                "pseudo" => "root_user",
                "address" => "somewhere in the universe",
                "place_residence" => "localhost",
                "identification_type_id" => IdentificationType::ID_CARD,
                "identification_doc" => "",
                "password" => Hash::make('password'),
                "role_id" => Role::ROOT,
            ],
            [
                "last_name" => "Admin",
                "id" => 2,
                "first_name" => "User",
                "email" => "admin@luggin.io",
                "phone" => "1xxxxxxxxx1",
                "pseudo" => "admin_user",
                "address" => "somewhere in the universe",
                "place_residence" => "localhost",
                "identification_doc" => "",
                "identification_type_id" => IdentificationType::ID_CARD,
                "password" => Hash::make('password'),
                "role_id" => Role::ADMIN,
            ],
        ];

        User::insert($data);
    }
}
