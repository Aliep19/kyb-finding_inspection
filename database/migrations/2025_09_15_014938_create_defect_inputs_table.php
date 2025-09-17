<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defect_inputs', function (Blueprint $table) {
            $table->id(); // PK
            $table->string('id_defect')->unique(); // kode unik (misal DEF20250915001)
            $table->date('tgl');
            $table->string('shift', 10);
            $table->string('npk', 20);
            $table->string('line', 50);
            $table->string('marking_number', 50)->nullable();
            $table->string('lot', 50)->nullable();
            $table->string('kayaba_no', 50)->nullable();
            $table->integer('total_check')->default(0);
            $table->integer('ok')->default(0);
            $table->integer('total_ng')->default(0);
            $table->integer('reject')->default(0);
            $table->integer('repair')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defect_inputs');
    }
};
