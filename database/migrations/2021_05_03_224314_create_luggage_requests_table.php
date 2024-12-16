<?php

use App\Models\LuggageRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLuggageRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('luggage_requests', function (Blueprint $table) {
            $table->id();
            $table->double("weight");
            $table->double("proposal_unit_price");
            $table->double("transaction_fees");
            $table->enum("state", LuggageRequest::getState())->default(LuggageRequest::STATE_PENDING);
            $table->enum("initiator", LuggageRequest::getInitiator());
            $table->longText("cancellation_reason")->nullable();
            $table->dateTime("cancel_at")->nullable();
            $table->dateTime("delivery_at")->nullable();
            $table->dateTime("delivery_request_at")->nullable();
            $table->boolean("is_assurance")->default(false);
            $table->foreignId('parcel_id')
                ->references('id')
                ->on('parcels')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('trip_id')
                ->references('id')
                ->on('trips')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('luggage_requests');
    }
}
