<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('local_reports', function (Blueprint $table) {
            $table->id();
            $table->string('branch')->nullable();
            $table->string('zone');
            $table->string('road');
            $table->json('public_complaints')->nullable();   // store multiple selections
            $table->text('public_others')->nullable();        // for 'Others' input
            $table->json('operations_complaints')->nullable();
            $table->text('operations_others')->nullable();
            $table->string('technician_name');
            $table->string('landmark')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('photos')->nullable();
            $table->json('videos')->nullable();
            $table->timestamp('created_at')->nullable();      // added created_at column
            $table->timestamp('updated_at')->nullable();      // added updated_at column
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_reports');
    }
};
