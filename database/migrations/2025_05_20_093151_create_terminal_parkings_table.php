<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('terminal_parkings', function (Blueprint $table) {
            $table->id();
            $table->string('terminal_id'); // FK to terminals
            $table->string('branch'); // optional, or get from terminals
            $table->string('status');
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('terminal_id')->references('id')->on('terminals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('terminal_parkings');
    }
};
