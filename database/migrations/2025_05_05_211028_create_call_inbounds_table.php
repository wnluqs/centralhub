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
        Schema::create('call_inbounds', function (Blueprint $table) {
            $table->id();
            $table->string('caller_name');
            $table->string('phone')->nullable();
            $table->timestamp('call_time');
            $table->string('category')->nullable();
            $table->text('notes')->nullable();
            $table->string('department_referred')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_inbounds');
    }
};
