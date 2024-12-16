<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnfidoUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onfido_users', function (Blueprint $table) {
            $table->id();
            $table->String("applicant_id")->default(true);
            $table->dateTime("id_verification_date")->nullable();
            $table->longText('verification_log')->nullable();
            $table->foreignId('owner_id')
                ->unique()
                ->constrained('users')
                ->onUpdate("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('onfido_users');
    }
}
