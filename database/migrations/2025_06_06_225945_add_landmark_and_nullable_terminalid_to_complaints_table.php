<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('landmark_description')->nullable()->after('road');
            $table->string('terminal_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('terminal_id')->change();
            $table->dropColumn('landmark_description');
        });
    }
};
