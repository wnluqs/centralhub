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
        Schema::create('terminal_parkings', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('status')->nullable();
            $table->string('zone_code')->nullable();
            $table->timestamp('last_communication')->nullable();
            $table->string('battery_health')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('terminal_parkings');
    }
};
