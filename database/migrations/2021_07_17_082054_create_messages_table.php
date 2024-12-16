<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->longText("message");
            $table->enum("type",\App\Models\Message::getType());
            $table->boolean("is_read")->default(false);
            $table->longText('attachment_thumb_url')->nullable();
            $table->longText('attachment_url')->nullable();
            $table->foreignId("conversation_id")
                ->references("id")
                ->on("conversations")
                ->onDelete("cascade")
                ->onUpdate("cascade");
            $table->foreignId("sender_id")
                ->references("id")
                ->on("users")
                ->onDelete("cascade")
                ->onUpdate("cascade");
            $table->double("price")->nullable();
            $table->double("weight")->nullable();
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
        Schema::dropIfExists('messages');
    }
}
