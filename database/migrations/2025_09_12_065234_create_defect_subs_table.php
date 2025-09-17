<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('defect_subs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_defect_category');
            $table->string('jenis_defect');
            $table->timestamps();

            $table->foreign('id_defect_category')->references('id')->on('defect_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defect_subs');
    }
};
