<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('terminals', function (Blueprint $table) {
            $table->string('id')->primary(); // Terminal ID as string, e.g., "VS001"
            $table->string('branch')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('terminals');
    }
};
