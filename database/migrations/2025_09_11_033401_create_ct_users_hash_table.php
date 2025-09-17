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
        Schema::create('ct_users_hash', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 255); // Nama karyawan
            $table->string('pwd', 220); // Password hash
            $table->integer('approved')->default(0); // 1 aktif, <>1 nonaktif
            $table->string('npk', 10)->unique(); // K1122, NPK unik
            $table->string('dept', 10)->nullable(); // Departemen
            $table->string('sect', 5)->nullable(); // Section
            $table->string('subsect', 5)->nullable(); // Subsection
            $table->integer('golongan')->nullable(); // 0-2 operator, 3 foreman, 4 acting
            $table->integer('acting')->nullable(); // 0-2 operator, 3 foreman, 4 acting
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('ct_users_hash');
    }
};
