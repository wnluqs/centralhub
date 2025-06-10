<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->text('photo_path')->nullable()->change();
            $table->longText('video_path')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->change();
            $table->string('video_path')->nullable()->change();
        });
    }
};
