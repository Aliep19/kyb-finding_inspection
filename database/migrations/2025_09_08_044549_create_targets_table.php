<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->integer('target_value');
            $table->unsignedTinyInteger('start_month'); // 1 = Jan, 12 = Des
            $table->year('start_year');
            $table->unsignedTinyInteger('end_month');
            $table->year('end_year');
            $table->timestamps();

            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
