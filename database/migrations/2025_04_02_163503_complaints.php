<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            // Define terminal_id as string
            $table->string('terminal_id');
            $table->foreign('terminal_id')
                ->references('id')->on('terminals')
                ->onDelete('cascade');

            // Zone, Road
            $table->string('zone')->nullable();
            $table->string('road')->nullable();

            // Photos
            $table->string('photos')->nullable();

            // Remarks
            $table->text('remarks')->nullable();

            // Status
            $table->enum('status', ['Complete', 'Failed', 'Almost']);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};
