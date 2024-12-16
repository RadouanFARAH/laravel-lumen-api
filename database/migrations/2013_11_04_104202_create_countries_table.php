<?php

use Database\Seeders\CountrySeeders;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('alpha3');
            $table->string('alpha2');
            $table->string('phone_code');
            $table->string('currency');
            $table->string('flag_svg')->nullable();
            $table->timestamps();
        });
        (new CountrySeeders())->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
