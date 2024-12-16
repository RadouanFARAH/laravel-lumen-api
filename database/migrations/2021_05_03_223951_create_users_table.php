<?php

use Database\Seeders\UserTableSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("last_name");
            $table->string("first_name");
            $table->string("phone");
            $table->string("email");
            $table->string("pseudo");
            $table->string("password");
            $table->string("address");
            $table->string("place_residence");
            $table->text("identification_doc");
            $table->text("about_me")->nullable();
            $table->text("birthdate")->nullable();
            $table->text("profile")->nullable();
            $table->foreignId("identification_type_id")
                ->references('id')
                ->on('identification_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId("country_id")
                ->default(1)
                ->references("id")
                ->on("countries")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->foreignId('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
        (new UserTableSeeder())->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
