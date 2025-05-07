<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('terminal_id');
            $table->foreign('terminal_id')
                ->references('id')->on('terminals')
                ->onDelete('cascade');

            // Zone, Road
            $table->string('zone')->nullable();
            $table->string('road')->nullable();

            // NEW: Unified Spare Parts (JSON field)
            $table->json('spare_parts')->nullable();

            // Status
            $table->enum('status', ['Complete', 'Failed', 'Almost']);

            // NEW: Separate Photo and Video Fields + Grading
            $table->string('photo_path')->nullable();
            $table->string('video_path')->nullable();
            $table->enum('spare_grade', ['A', 'B', 'C'])->nullable();

            // Technician Name
            $table->string('technician_name')->nullable();

            // NEW: Optional branch assignment if needed per inspection (redundant if fetched from user)
            $table->string('branch')->nullable();
            // 7 New Fields
            $table->string('screen')->nullable();
            $table->string('keypad')->nullable();
            $table->string('sticker')->nullable();
            $table->string('solar')->nullable();
            $table->string('environment')->nullable();
            $table->string('spotcheck_verified')->nullable(); // or use boolean if only true/false
            $table->string('spotcheck_verified_by')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspections');
    }
};
