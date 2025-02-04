<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->string('id');
            $table->string("transaction_id");
            $table->string("credit_wallet_id");
            $table->primary("id");

            $table->foreignId("owner_id")
                ->references("id")
                ->on("users")
                ->onUpdate("cascade")
                ->onDelete("cascade");

            $table->foreign("transaction_id")
                ->references("id")
                ->on("transactions")
                ->onUpdate("cascade")
                ->onDelete("cascade");

            $table->foreign("credit_wallet_id")
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
        Schema::dropIfExists('wallets');
    }
}
