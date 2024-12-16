<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methodes', function (Blueprint $table) {
            $table->id();
            $table->longText("card_number");
            $table->integer("expiry_month")->nullable();
            $table->integer("expiry_year")->nullable();
            $table->longText("cvv")->nullable();
            $table->string("cart_type");
            $table->string("card_provider");
            $table->boolean("is_default")->default(false);
            $table->foreignId("owner_id")
            ->references("id")
            ->on("users")
            ->onUpdate("cascade")
            ->onDelete("cascade");
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
        Schema::dropIfExists('payment_methodes');
    }
}
