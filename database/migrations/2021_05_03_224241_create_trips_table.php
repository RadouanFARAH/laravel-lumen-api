<?php

use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->enum("parcel_restriction", Trip::getParcelRestriction());
            $table->string("fly_number");
            $table->dateTime("departure_date");
            $table->dateTime("arrival_date");
            $table->double("available_weight");
            $table->double("booked_weight")->default(0);
            $table->double("weight_unit_price");
            $table->boolean("auto_accept_booking")->default(false);
            $table->boolean("allow_split_luggage")->default(false);
            $table->boolean("canceled")->default(false);
            $table->longText("info")->nullable();
            $table->longText("cancellation_reason")->nullable();

            $table->foreignId('traveler_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('departure_city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('departure_airport_id')
                ->references('id')
                ->on('airports')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('arrival_city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('arrival_airport_id')
                ->references('id')
                ->on('airports')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('trips');
    }
}
