<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->string("key");
            $table->boolean("commission_received")->default(false);
            $table->foreignId('child_id')
                ->constrained('users')
                ->onUpdate("cascade");
            $table->foreignId('parent_id')
                ->constrained('users')
                ->onUpdate("cascade");

            $table->primary(["child_id"]);
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
        Schema::dropIfExists('referrals');
    }
}
