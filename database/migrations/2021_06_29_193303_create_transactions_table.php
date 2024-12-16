<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('account');
            $table->double('balance');
            $table->timestamp('verify_at')->nullable();
            $table->timestamp('init_at')->useCurrent();
            $table->longText('verification_log')->nullable();
            $table->string('payment_token')->default("INVALID");
            $table->string('reason');
            $table->foreignId('movement_type_id')
                ->references('id')
                ->on('movement_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
        Schema::dropIfExists('transactions');
    }
}
