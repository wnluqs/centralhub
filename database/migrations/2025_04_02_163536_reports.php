<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // Define terminal_id as string
            $table->string('terminal_id');
            $table->foreign('terminal_id')
                ->references('id')->on('terminals')
                ->onDelete('cascade');

            // Spare Part 1, 2, 3
            $table->string('spare_part_1')->nullable();
            $table->string('spare_part_2')->nullable();
            $table->string('spare_part_3')->nullable();

            // Status
            $table->enum('status', ['Complete', 'Failed', 'Almost']);

            $table->timestamps(); // includes created_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
