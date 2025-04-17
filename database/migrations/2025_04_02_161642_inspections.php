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

            // Spare Part 1, 2, 3
            $table->string('spare_part_1')->nullable();
            $table->string('spare_part_2')->nullable();
            $table->string('spare_part_3')->nullable();

            // Status
            $table->enum('status', ['Complete', 'Failed', 'Almost']);

            // Photos
            $table->string('photos')->nullable(); // store file path

            // Technician Name
            $table->string('technician_name')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspections');
    }
};
