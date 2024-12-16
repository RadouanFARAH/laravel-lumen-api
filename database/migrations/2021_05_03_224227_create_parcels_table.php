<?php

use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->enum("parcel_restriction", Trip::getParcelRestriction());
            $table->timestamp("departure_date");
            $table->timestamp("arrival_date");
            $table->double("weight");
            $table->double("weight_unit_price");
            $table->double("booked_weight")->default(0);
            $table->string("fly_number")->nullable();
            $table->boolean("private")->default(false);
            $table->timestamp("edition_locked_at")->nullable();
            $table->longText("images")->nullable();
            $table->longText("info")->nullable();
            $table->boolean("canceled")->default(false);
            $table->longText("cancellation_reason")->nullable();
            $table->boolean("allow_split")->default(false);
            $table->foreignId('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('recipient_id')
                ->references('id')
                ->on('recipients')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('departure_airport_id')
                ->nullable()
                ->references('id')
                ->on('airports')
                ->onDelete('cascade')
                ->onUpdate('cascade');


            $table->foreignId('departure_city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('arrival_airport_id')
                ->nullable()
                ->references('id')
                ->on('airports')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('arrival_city_id')
                ->references('id')
                ->on('cities')
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
        Schema::dropIfExists('parcels');
    }
}
