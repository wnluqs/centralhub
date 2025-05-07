<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ftlt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('staff_id')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->string('checkin_photo')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->string('checkout_photo')->nullable();
            $table->string('zone')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ftlt');
    }
};
