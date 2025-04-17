<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event'); // e.g., 'login', 'logout', 'created', 'updated', 'deleted'
            $table->string('model')->nullable(); // e.g., 'Complaint', 'Inspection'
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the model changed
            $table->text('description')->nullable(); // Additional info or changes
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
