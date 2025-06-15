<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ftlt', function (Blueprint $table) {
            $table->string('branch')->nullable()->after('zone');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ftlt', function (Blueprint $table) {
            //
        });
    }
};
