<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string("channel");
            $table->foreignId("creator_id")
            ->references("id")
            ->on("users")
            ->onDelete("cascade")
            ->onUpdate("cascade");
            $table->foreignId("trip_id")
                ->nullable()
                ->references("id")
                ->on("trips")
                ->onDelete("cascade")
                ->onUpdate("cascade");
            $table->foreignId("parcel_id")
                ->nullable()
                ->references("id")
                ->on("parcels")
                ->onDelete("cascade")
                ->onUpdate("cascade");
            $table->foreignId("request_id")
                ->nullable()
                ->references("id")
                ->on("luggage_requests")
                ->onDelete("cascade")
                ->onUpdate("cascade");
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
        Schema::dropIfExists('conversations');
    }
}
