<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waliby_job_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->foreign('event_id')->references('id')->on('waliby_events')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->datetime('reserved_at');
            $table->datetime('finished_at');
            $table->enum('status', ['success', 'error']);
            $table->text('exception')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waliby_job_logs');
    }
};
