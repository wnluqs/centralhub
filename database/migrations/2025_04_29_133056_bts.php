<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('bts'); // drop existing table safely if needed

        Schema::create('bts', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->nullable(); // assuming 'T001', etc.
            $table->string('terminal_id');
            $table->string('status'); // Warning, Error
            $table->string('location');
            $table->dateTime('event_date');
            $table->string('event_code_name');
            $table->text('comment')->nullable();
            $table->string('parts_request')->nullable();
            $table->unsignedBigInteger('action_by')->nullable(); // Technician User ID
            $table->string('action_status')->default('New'); // New / In Progress / Resolved
            $table->string('photo')->nullable();
            $table->enum('terminal_status', ['Okay', 'Off'])->nullable();
            $table->string('damage_type')->nullable();
            $table->boolean('verified')->default(0);
            $table->timestamps();

            // If you want to create relationship with users table:
            $table->foreign('action_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bts');
    }
};
