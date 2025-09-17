<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp', function (Blueprint $table) {
            $table->id();
            $table->string('npk', 10);
            $table->string('no_hp', 20);
            $table->string('kode_otp', 6);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp');
    }
};
