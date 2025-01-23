<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('waliby_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->enum('event_type', ['manual', 'recurring']);
            $table->uuid('message_template_id');
            $table->foreign('message_template_id')->references('id')->on('waliby_message_templates')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->text('receiver_params');
            $table->datetime('last_processed')->nullable();
            $table->enum('scheduled_every', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->integer('scheduled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waliby_events');
    }
}
