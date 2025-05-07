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
        Schema::create('report', function (Blueprint $table) {
            $table->id();
            $table->string('terminal_id');
            $table->string('location');
            $table->datetime('event_date');
            $table->string('event_code_name');
            $table->text('comment')->nullable();
            $table->string('parts_request');
            $table->string('photo')->nullable();
            $table->enum('terminal_status', ['Okay', 'Off']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report');
    }
};
