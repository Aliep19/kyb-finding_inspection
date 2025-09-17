<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Arahkan migration ini ke koneksi DB lembur
    protected $connection = 'lembur';

    public function up(): void
    {
        Schema::create('hrd_so', function (Blueprint $table) {
            $table->id();
            $table->string('npk', 10)->nullable()->index(); // Relasi ke NPK
            $table->integer('tipe')->nullable(); // 1 : Manager, 2 : Expert, dst
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('hrd_so');
    }
};
