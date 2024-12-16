<?php

use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->enum("context",\App\Models\SearchHistory::getContext());
            $table->string("from")->nullable();
            $table->string("to")->nullable();
            $table->date("fly_number")->nullable();
            $table->date("date")->nullable();
            $table->enum("parcel_restriction", Trip::getParcelRestriction());
            $table->boolean("saved")->default(false);
            $table->boolean("is_alert")->default(false);
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('search_histories');
    }
}
