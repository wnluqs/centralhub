<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('battery_replacements', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->nullable(); // Assigned technician
            $table->string('terminal_id'); // e.g. KN1A01
            $table->enum('status', ['Assigned', 'Submitted'])->default('Assigned');
            $table->string('photo')->nullable(); // Must come from camera
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battery_replacements');
    }
};
