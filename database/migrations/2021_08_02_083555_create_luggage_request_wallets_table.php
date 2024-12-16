<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLuggageRequestWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('luggage_request_wallets', function (Blueprint $table) {
            $table->id();
            $table->string("wallet_id");

            $table->foreignId("luggage_request_id")
                ->references("id")
                ->on("luggage_requests")
                ->onUpdate("cascade")
                ->onDelete("cascade");

            $table->foreign("wallet_id")
                ->references("id")
                ->on("wallets")
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
        Schema::dropIfExists('luggage_request_wallets');
    }
}
