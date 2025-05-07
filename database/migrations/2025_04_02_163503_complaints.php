<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('terminal_id');
            $table->foreign('terminal_id')->references('id')->on('terminals')->onDelete('cascade');

            $table->string('zone')->nullable();
            $table->string('road')->nullable();

            $table->json('photos')->nullable(); // for multiple image uploads
            $table->text('remarks')->nullable();
            $table->string('types_of_damages')->nullable(); // e.g., 'Damaged', 'Missing', 'Broken'

            $table->string('status')->default('New'); // New, In Progress, Awaiting Confirmation, Closed
            $table->unsignedBigInteger('assigned_to')->nullable(); // linked to user table
            $table->timestamp('attended_at')->nullable();
            $table->timestamp('fixed_at')->nullable();
            $table->text('fix_comment')->nullable();
            $table->enum('terminal_status', ['Okay', 'Off'])->nullable();
            $table->boolean('verified')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};
